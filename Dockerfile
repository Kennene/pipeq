# King Debian
FROM debian:bookworm as PipeQ

# Set environment variables
ENV LANG en_US.UTF-8
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install system dependencies
RUN apt-get update && apt-get install -y \
    locales \
    #!
    iproute2 \
    nano \
    #!
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

#? Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to Laravel's public directory
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i '/<Directory ${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy application code
WORKDIR /var/www/html
COPY . /var/www/html

# Install project dependencies
RUN composer install
RUN npm install
RUN php artisan migrate:fresh --seed

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database/database.sqlite

# Expose Apache port
EXPOSE 80

# Start Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

