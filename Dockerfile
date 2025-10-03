# ---- Dockerfile (v2 mínimo, sin gd) ----
FROM php:5.6-apache

# Repos archivados (Debian viejo) y utilidades necesarias
RUN sed -i 's/deb.debian.org/archive.debian.org/g; s|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
 && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid \
 && apt-get update -y \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
      libpq-dev libxml2-dev zlib1g-dev \
 && docker-php-ext-install mbstring xml pgsql pdo_pgsql \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Zona horaria
RUN echo "date.timezone = America/La_Paz" > /usr/local/etc/php/conf.d/timezone.ini

# Copia TestLink al DocumentRoot
COPY . /var/www/html/

# Permisos de escritura que exige TestLink
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/gui/templates_c /var/www/html/logs /var/www/html/upload_area || true

# Ajuste del puerto $PORT para Render
COPY run.sh /run.sh
RUN chmod +x /run.sh

EXPOSE 10000
CMD ["/run.sh"]