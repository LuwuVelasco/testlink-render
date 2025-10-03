# Usa PHP 5.6 + Apache (necesario para TestLink 1.9.0)
FROM php:5.6-apache

# Repos archivados (Debian Jessie) para que apt funcione
RUN sed -i 's/deb.debian.org/archive.debian.org/g; s|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
 && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid \
 && apt-get update -y \
 && apt-get install -y --no-install-recommends \
      libpq-dev libxml2-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev zlib1g-dev libzip-dev \
 && docker-php-ext-install mbstring xml \
 && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
 && docker-php-ext-install gd \
 && docker-php-ext-install pgsql pdo_pgsql \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Zona horaria
RUN echo "date.timezone = America/La_Paz" > /usr/local/etc/php/conf.d/timezone.ini

# Copia el código de TestLink al DocumentRoot
COPY . /var/www/html/

# Permisos: carpetas que TestLink necesita escribir
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/gui/templates_c /var/www/html/logs /var/www/html/upload_area || true

# Ajusta Apache para escuchar en $PORT (que Render define, típico 10000)
COPY run.sh /run.sh
RUN chmod +x /run.sh

EXPOSE 10000
CMD ["/run.sh"]
