#!/bin/bash
: "${PORT:=10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf
export PGSSLMODE="require"
export PGOPTIONS="endpoint=ep-silent-sun-afd0euia"
exec apache2-foreground
