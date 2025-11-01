# ---- TestLink 1.9.0 en PHP 7.4 + Apache (Render) con Neon PostgreSQL ----
FROM php:7.4-apache

# Instalar dependencias para PHP 7.4
RUN set -eux; \
  apt-get update; \
  DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    ca-certificates curl build-essential \
    libpq-dev libxml2-dev zlib1g-dev \
    libjpeg62-turbo-dev libfreetype6-dev libpng-dev \
    libzip-dev libonig-dev unzip; \
  docker-php-ext-install mbstring xml zip; \
  docker-php-ext-install pgsql pdo_pgsql; \
  docker-php-ext-configure gd --with-freetype --with-jpeg; \
  docker-php-ext-install gd; \
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
