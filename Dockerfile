# King Debian
FROM debian:bookworm as PipeQ

ARG REVERB_PORT

# Set environment variables
ENV LANG C.UTF-8
ENV LC_ALL C.UTF-8
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install system dependencies
RUN apt-get update && apt-get install -y \
    locales \
    #! Tools for troubleshooting container. Remove for final release.
    iproute2 \
    nano \
    #!
    ca-certificates\
    zip \
    unzip \
    curl \
    supervisor \
    apache2 \
    libapache2-mod-php \
    php \
    php-cli \
    php-zip \
    php-xml \
    php-curl \
    php-sqlite3 \
    composer \
    npm \
    && apt-get clean
    #! && rm -rf /var/lib/apt/lists/*

# Configure locale
RUN localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to Laravel's public directory
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -i '/<Directory ${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy application code
WORKDIR /var/www/html
COPY . /var/www/html

# Install project dependencies and create database
RUN composer install \
    && npm install \
    && npm run build \
    && php artisan migrate:fresh --seed

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose Apache port and WebSockets port
EXPOSE 80
EXPOSE ${REVERB_PORT}

# Start Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
