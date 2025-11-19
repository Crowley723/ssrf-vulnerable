FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY init-db.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/init-db.sh

RUN mkdir -p /var/www/data \
    && chown -R www-data:www-data /var/www/data

EXPOSE 80

CMD ["/bin/bash", "-c", "/usr/local/bin/init-db.sh && apache2-foreground"]
