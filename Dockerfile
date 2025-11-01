# ---- TestLink 1.9.0 en PHP 5.6 + Apache (Render) ----
FROM php:5.6-apache

# Repos archivados, QUITAR stretch-updates y permitir repos vencidos
RUN set -eux; \
  sed -i 's/deb.debian.org/archive.debian.org/g; s|security.debian.org|archive.debian.org|g' /etc/apt/sources.list; \
  sed -i '/stretch-updates/d' /etc/apt/sources.list; \
  printf 'Acquire::Check-Valid-Until "false";\nAcquire::AllowInsecureRepositories "true";\nAPT::Get::AllowUnauthenticated "true";\n' > /etc/apt/apt.conf.d/99archive; \
  apt-get -o Acquire::Check-Valid-Until=false update; \
  DEBIAN_FRONTEND=noninteractive apt-get -o APT::Get::AllowUnauthenticated=true install -y --no-install-recommends \
    ca-certificates curl build-essential \
    libpq-dev libxml2-dev zlib1g-dev \
    libjpeg62-turbo-dev libfreetype6-dev libpng-dev || true; \
  (apt-get -o APT::Get::AllowUnauthenticated=true install -y --no-install-recommends libpng12-dev || true); \
  docker-php-ext-install mbstring xml; \
  docker-php-ext-install pgsql pdo_pgsql; \
  docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ || true; \
  (docker-php-ext-install gd || true); \
  a2enmod rewrite; \
  rm -rf /var/lib/apt/lists/*

# Decirle a Apache/PHP que la petición original es HTTPS (Render usa X-Forwarded-Proto)
RUN a2enmod headers && \
    printf 'SetEnvIf X-Forwarded-Proto "^https$" HTTPS=on\n' > /etc/apache2/conf-available/forwarded-https.conf && \
    a2enconf forwarded-https

# Copiar configuración para pasar variables de entorno a PHP
COPY apache-env.conf /etc/apache2/conf-available/apache-env.conf
RUN a2enconf apache-env

# Zona horaria de PHP
RUN echo "date.timezone = America/La_Paz" > /usr/local/etc/php/conf.d/timezone.ini

# Overrides PHP útiles (subidas/memoria/timeout)
RUN { \
  echo 'upload_max_filesize = 32M'; \
  echo 'post_max_size = 32M'; \
  echo 'memory_limit = 256M'; \
  echo 'max_execution_time = 120'; \
} > /usr/local/etc/php/conf.d/zz-overrides.ini

# Copia el código de TestLink
COPY . /var/www/html/

# Normaliza CRLF por si run.sh viene de Windows
RUN sed -i 's/\r$//' /var/www/html/run.sh || true

# Permisos que TestLink necesita
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/gui/templates_c /var/www/html/logs /var/www/html/upload_area || true

# Ajuste del puerto dinámico de Render
COPY run.sh /run.sh
RUN chmod +x /run.sh
EXPOSE 10000
CMD ["/run.sh"]
