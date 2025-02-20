# ---- Build Stage ----
FROM php:8.3-fpm AS builder

# Set working directory
WORKDIR /pipeq

# Install build dependencies
RUN apt-get update && apt-get install --no-install-recommends --assume-yes \
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
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
RUN npm clean-install --no-audit && npm run build


# ---- Final Stage ----
FROM php:8.3-fpm AS production

# Set working directory
WORKDIR /var/www/html

# Install runtime dependencies: Nginx, Supervisor, etc.
RUN apt-get update && apt-get install --no-install-recommends -y \
    nginx \
    supervisor \
    libzip-dev \
    && docker-php-ext-install zip pcntl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy built application from builder stage
COPY --chown=www-data:www-data --from=builder /pipeq /var/www/html

# Remove the default Nginx config and add our custom configuration
RUN rm -f /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose HTTP and reverb ports
EXPOSE 80 8080

# Initialize database if it doesn't exist
ENTRYPOINT ["./docker-entrypoint.sh"]

# Start Supervisor (which in turn starts PHP-FPM, Nginx and the reverb server)
CMD ["/usr/bin/supervisord", "-n", "-c", "/var/www/html/supervisord.conf"]