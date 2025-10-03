# ---- TestLink 1.9.0 en PHP 5.6 + Apache (Render) ----
FROM php:5.6-apache

# Usar snapshots archivados de Debian Jessie y permitir repos “vencidos”
RUN set -eux; \
  sed -i 's/deb.debian.org/archive.debian.org/g; s/security.debian.org/archive.debian.org/g' /etc/apt/sources.list; \
  printf 'Acquire::Check-Valid-Until "false";\nAcquire::AllowInsecureRepositories "true";\n' > /etc/apt/apt.conf.d/99archive; \
  apt-get -o Acquire::Check-Valid-Until=false update; \
  DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    ca-certificates curl build-essential \
    libpq-dev libxml2-dev zlib1g-dev \
    libjpeg62-turbo-dev libfreetype6-dev \
    libpng-dev || true; \
  # fallback para entornos donde libpng-dev no está (muy viejo)
  (apt-get install -y --no-install-recommends libpng12-dev || true); \
  docker-php-ext-install mbstring xml; \
  docker-php-ext-install pgsql pdo_pgsql; \
  # GD (si no compila, no falla el build gracias a '|| true')
  docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ || true; \
  (docker-php-ext-install gd || true); \
  a2enmod rewrite; \
  rm -rf /var/lib/apt/lists/*

# Zona horaria
RUN echo "date.timezone = America/La_Paz" > /usr/local/etc/php/conf.d/timezone.ini

# Copia código de TestLink (tu repo completo) al DocumentRoot
COPY . /var/www/html/

# Normaliza fin de línea por si el repo es CRLF (Windows)
RUN sed -i 's/\r$//' /var/www/html/run.sh || true

# Permisos que TestLink necesita escribir
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/gui/templates_c /var/www/html/logs /var/www/html/upload_area || true

# Ajuste de puerto dinámico ($PORT) para Render
COPY run.sh /run.sh
RUN chmod +x /run.sh
EXPOSE 10000
CMD ["/run.sh"]