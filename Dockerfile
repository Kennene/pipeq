# ---- Build Stage ----
FROM php:8.3-fpm AS builder

# Set working directory
WORKDIR /pipeq

# Install build dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install zip

# Install latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application, install dependencies, and build
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build 


# ---- Final Stage ----
FROM php:8.3-fpm AS production

# Set working directory
WORKDIR /var/www/html

# Install runtime dependencies: Nginx, Supervisor, etc.
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-install zip pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy built application from builder stage
COPY --from=builder /pipeq /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache database

# Optimize application
RUN php artisan optimize:clear && php artisan optimize

# Remove the default Nginx config and add our custom configuration
RUN rm /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose HTTP and reverb ports
EXPOSE 80 8080

# Fix permissions in mounted volumes
RUN chmod +x docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

# Start Supervisor (which in turn starts PHP-FPM, Nginx and the reverb server)
CMD ["/usr/bin/supervisord", "-n", "-c", "/var/www/html/supervisord.conf"]
