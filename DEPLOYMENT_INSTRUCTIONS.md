# Instrucciones de Despliegue - TestLink con Neon PostgreSQL

## Cambios Realizados

He realizado los siguientes cambios para solucionar el problema de conexión a la base de datos Neon:

### 1. **config_db.inc.php** - Configuración Mejorada
- Cambiado `DB_TYPE` de `'pgsql'` a `'postgres8'` para mejor compatibilidad con ADODB
- Agregado validación de variables de entorno
- Mejorado el manejo de puerto y opciones SSL
- Agregado soporte para formato flexible de `PGOPTIONS`

### 2. **db_neon_fix.php** - Helper de Conexión
- Nuevo archivo helper para configurar correctamente la conexión ADODB con Neon
- Configura variables de entorno de PostgreSQL
- Valida extensiones necesarias

### 3. **Archivos de Diagnóstico**
- `dbcheck.php` - Mejorado con más información de debug
- `test_db_connection.php` - Nuevo archivo para diagnóstico completo

## Pasos para Resolver el Problema

### Paso 1: Verificar Variables de Entorno en Render

En el dashboard de Render (https://dashboard.render.com), ve a tu servicio `testlink-web` y verifica que las siguientes variables de entorno estén configuradas correctamente:

```
TL_DB_HOST=ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech
TL_DB_PORT=5432
TL_DB_NAME=neondb
TL_DB_USER=neondb_owner
TL_DB_PASS=[tu-password-de-neon]
PGSSLMODE=require
PGOPTIONS=endpoint=ep-silent-sun-afd0euia
TL_TIMEZONE=America/La_Paz
```

**IMPORTANTE:** El host configurado es con `c-2` según tu configuración actual en Render. Verifica que el `TL_DB_HOST` coincida exactamente con el que aparece en tu dashboard de Neon.

### Paso 2: Actualizar render.yaml (Si es necesario)

Si el host en Neon es diferente al que está en `render.yaml` línea 15, actualízalo:

```yaml
      - key: TL_DB_HOST
        value: [tu-host-correcto-de-neon]
```

### Paso 3: Commit y Push de los Cambios

```bash
git add .
git commit -m "Fix: Configuración de conexión a Neon PostgreSQL"
git push origin main
```

Render detectará automáticamente los cambios y redesplegará la aplicación.

### Paso 4: Verificar la Conexión

Una vez que el despliegue termine, verifica la conexión accediendo a:

1. **Diagnóstico básico:**
   ```
   https://testlink-web.onrender.com/dbcheck.php
   ```

2. **Diagnóstico completo:**
   ```
   https://testlink-web.onrender.com/test_db_connection.php
   ```

Si ves un mensaje de éxito (✓ Conexión exitosa!), entonces la base de datos está conectada correctamente.

### Paso 5: Acceder a TestLink

Intenta acceder a tu aplicación:
```
https://testlink-web.onrender.com
```

## Problemas Comunes y Soluciones

### Error: "Variables de entorno no están definidas"
- Verifica que todas las variables estén configuradas en Render
- Asegúrate de que `TL_DB_PASS` esté configurada como "Secret File" en Render

### Error: "could not connect to server"
- Verifica que el `TL_DB_HOST` sea exactamente el mismo que aparece en Neon
- Asegúrate de que el endpoint en `PGOPTIONS` sea correcto (sin el sufijo `-pooler`)

### Error: "SSL connection required"
- Verifica que `PGSSLMODE=require` esté configurado en las variables de entorno

### Error: "Extension pgsql not loaded"
- El Dockerfile ya incluye la instalación de la extensión pgsql
- Si aparece este error, puede ser un problema con el build del contenedor

## Verificación de la Configuración Correcta

Para verificar que todo está bien configurado, los archivos de diagnóstico deben mostrar:

```
✓ Conexión exitosa con pg_connect!
✓ Conexión exitosa con ADODB!
PostgreSQL Version: PostgreSQL 16.x on x86_64-pc-linux-gnu...
```

## Información Adicional

### Formato de Conexión para Neon

Neon usa un formato especial de conexión:
```
host=<endpoint>-pooler.<region>.aws.neon.tech
port=5432
sslmode=require
options=endpoint=<endpoint>
```

Donde:
- `<endpoint>` es tu endpoint único (ej: `ep-silent-sun-afd0euia`)
- `<region>` es la región (ej: `c-2.us-west-2`)

### Sobre ADODB y PostgreSQL

TestLink 1.9.0 usa ADODB 5.x para conectarse a la base de datos. ADODB es una capa de abstracción de base de datos para PHP que soporta múltiples DBMS.

Para PostgreSQL con SSL, ADODB usa las variables de entorno `PGSSLMODE` y `PGOPTIONS`, que hemos configurado correctamente en los archivos.

## Contacto

Si después de seguir estos pasos el problema persiste, revisa los logs en Render:
1. Ve a tu servicio en Render
2. Click en "Logs" en el menú lateral
3. Busca mensajes de error relacionados con la base de datos

Los errores comunes aparecerán como:
- "Database connection failed"
- "FATAL: password authentication failed"
- "could not connect to server"
