#!/bin/bash
# Script de verificación de configuración antes de desplegar

echo "============================================"
echo "Verificación de Configuración - TestLink"
echo "============================================"
echo ""

ERROR=0

# Verificar archivos críticos
echo "✓ Verificando archivos críticos..."

if [ ! -f "config_db.inc.php" ]; then
    echo "  ✗ config_db.inc.php no existe"
    ERROR=1
else
    echo "  ✓ config_db.inc.php existe"
fi

if [ ! -f "render.yaml" ]; then
    echo "  ✗ render.yaml no existe"
    ERROR=1
else
    echo "  ✓ render.yaml existe"
fi

if [ ! -f "Dockerfile" ]; then
    echo "  ✗ Dockerfile no existe"
    ERROR=1
else
    echo "  ✓ Dockerfile existe"
fi

if [ ! -f "run.sh" ]; then
    echo "  ✗ run.sh no existe"
    ERROR=1
else
    echo "  ✓ run.sh existe"
fi

if [ ! -f "apache-env.conf" ]; then
    echo "  ✗ apache-env.conf no existe"
    ERROR=1
else
    echo "  ✓ apache-env.conf existe"
fi

echo ""
echo "✓ Verificando configuración en render.yaml..."

# Verificar que render.yaml tenga las variables correctas
if grep -q "TL_DB_HOST" render.yaml; then
    HOST=$(grep "TL_DB_HOST" render.yaml -A 1 | tail -n 1 | sed 's/.*value: //' | tr -d ' ')
    echo "  ✓ TL_DB_HOST configurado: $HOST"
else
    echo "  ✗ TL_DB_HOST no encontrado en render.yaml"
    ERROR=1
fi

if grep -q "TL_DB_NAME" render.yaml; then
    DBNAME=$(grep "TL_DB_NAME" render.yaml -A 1 | tail -n 1 | sed 's/.*value: //' | tr -d ' ')
    echo "  ✓ TL_DB_NAME configurado: $DBNAME"
else
    echo "  ✗ TL_DB_NAME no encontrado en render.yaml"
    ERROR=1
fi

if grep -q "PGSSLMODE" render.yaml; then
    echo "  ✓ PGSSLMODE configurado"
else
    echo "  ✗ PGSSLMODE no encontrado en render.yaml"
    ERROR=1
fi

if grep -q "PGOPTIONS" render.yaml; then
    echo "  ✓ PGOPTIONS configurado"
else
    echo "  ✗ PGOPTIONS no encontrado en render.yaml"
    ERROR=1
fi

echo ""
echo "✓ Verificando config_db.inc.php..."

if grep -q "postgres8" config_db.inc.php; then
    echo "  ✓ DB_TYPE configurado como 'postgres8'"
else
    echo "  ✗ DB_TYPE no está configurado como 'postgres8'"
    ERROR=1
fi

if grep -q "getenv('TL_DB_HOST')" config_db.inc.php; then
    echo "  ✓ Lectura de variables de entorno configurada"
else
    echo "  ✗ Variables de entorno no configuradas correctamente"
    ERROR=1
fi

echo ""
echo "✓ Verificando permisos de run.sh..."

if [ -x "run.sh" ] || [ "$OSTYPE" == "msys" ]; then
    echo "  ✓ run.sh tiene permisos de ejecución"
else
    echo "  ! run.sh no tiene permisos de ejecución (se configurará en el contenedor)"
fi

echo ""
echo "============================================"

if [ $ERROR -eq 0 ]; then
    echo "✅ VERIFICACIÓN EXITOSA"
    echo ""
    echo "Todo está configurado correctamente."
    echo "Puedes proceder con:"
    echo "  git add ."
    echo "  git commit -m 'Fix: Conexión a Neon PostgreSQL'"
    echo "  git push origin main"
else
    echo "❌ VERIFICACIÓN FALLIDA"
    echo ""
    echo "Hay problemas con la configuración."
    echo "Por favor revisa los errores arriba."
    exit 1
fi

echo "============================================"
