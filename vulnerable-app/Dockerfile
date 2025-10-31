FROM php:8.2-apache

# Enable Apache mod_rewrite (useful for routing)
RUN a2enmod rewrite

# Install additional PHP extensions you might need
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Install Redis PHP extension (for connecting to Redis)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www/html

# Copy PHP configuration if needed (optional)
COPY php.ini /usr/local/etc/php/

# Give Apache permission to write (needed for webshell exploit)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Allow .htaccess overrides (optional but useful)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]