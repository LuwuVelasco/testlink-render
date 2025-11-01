#!/bin/bash
set -e

# Configurar puerto dinámico de Render
: "${PORT:=10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Exportar variables de entorno de PostgreSQL para que estén disponibles en PHP
export PGSSLMODE="${PGSSLMODE:-require}"
export PGOPTIONS="${PGOPTIONS:-endpoint=ep-silent-sun-afd0euia}"

# Exportar variables de conexión de base de datos
export TL_DB_HOST="${TL_DB_HOST}"
export TL_DB_PORT="${TL_DB_PORT:-5432}"
export TL_DB_NAME="${TL_DB_NAME}"
export TL_DB_USER="${TL_DB_USER}"
export TL_DB_PASS="${TL_DB_PASS}"
export TL_TIMEZONE="${TL_TIMEZONE:-America/La_Paz}"

# Verificar que las variables críticas estén definidas
if [ -z "$TL_DB_HOST" ] || [ -z "$TL_DB_NAME" ] || [ -z "$TL_DB_USER" ] || [ -z "$TL_DB_PASS" ]; then
    echo "ERROR: Variables de entorno de base de datos no están definidas"
    echo "TL_DB_HOST=${TL_DB_HOST}"
    echo "TL_DB_PORT=${TL_DB_PORT}"
    echo "TL_DB_NAME=${TL_DB_NAME}"
    echo "TL_DB_USER=${TL_DB_USER}"
    echo "TL_DB_PASS=${TL_DB_PASS:+***}"
    exit 1
fi

echo "Iniciando Apache con las siguientes configuraciones:"
echo "Puerto: $PORT"
echo "DB Host: $TL_DB_HOST"
echo "DB Name: $TL_DB_NAME"
echo "SSL Mode: $PGSSLMODE"
echo "PG Options: $PGOPTIONS"

# Iniciar Apache
exec apache2-foreground
