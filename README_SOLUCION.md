# 🔧 Solución: Conexión TestLink a Neon PostgreSQL

## 📋 Resumen del Problema

Tu aplicación TestLink en Render no se conectaba a la base de datos Neon PostgreSQL, mostrando el error "Database connection failed".

## ✅ Cambios Realizados

He realizado los siguientes cambios para resolver el problema:

### 1. **config_db.inc.php** ✏️
- Cambiado `DB_TYPE` de `'pgsql'` a `'postgres8'` (mejor compatibilidad con ADODB antiguo)
- Agregado validación de variables de entorno
- Mejorado manejo de puerto y SSL
- Configuración correcta de `PGOPTIONS` para Neon

### 2. **render.yaml** ✏️
- Host configurado correctamente como `c-2` según tu configuración en Render
- Variables de entorno correctamente configuradas

### 3. **run.sh** ✏️
- Agregadas validaciones de variables de entorno antes de iniciar Apache
- Exportación explícita de todas las variables necesarias
- Mensajes de debug para facilitar troubleshooting

### 4. **Dockerfile** ✏️
- Agregada configuración de Apache para pasar variables de entorno a PHP
- Nuevo archivo `apache-env.conf` para PassEnv

### 5. **Nuevos Archivos de Diagnóstico** 📊
- `dbcheck.php` - Mejorado con más información
- `test_db_connection.php` - Diagnóstico completo
- `lib/functions/db_neon_fix.php` - Helper para conexión ADODB

---

## 🚀 Pasos para Desplegar

### Paso 1: Verificar Variables de Entorno en Render

1. Ve a tu dashboard de Render: https://dashboard.render.com
2. Selecciona tu servicio `testlink-web`
3. Ve a "Environment" en el menú lateral
4. **VERIFICA** que estas variables estén configuradas:

```
TL_DB_HOST=ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech
TL_DB_PORT=5432
TL_DB_NAME=neondb
TL_DB_USER=neondb_owner
TL_DB_PASS=npg_B0WARUtcIvxF  (o tu password actual)
PGSSLMODE=require
PGOPTIONS=endpoint=ep-silent-sun-afd0euia
TL_TIMEZONE=America/La_Paz
```

**⚠️ IMPORTANTE:** 
- El `TL_DB_HOST` debe ser exactamente el que aparece en tu panel de Neon (en tu caso es con `c-2`)
- La password debe estar configurada como variable normal o como Secret

### Paso 2: Verificar el Host de Neon

Ve a tu dashboard de Neon y copia el connection string. Debe verse así:

```
postgresql://neondb_owner:PASSWORD@ep-silent-sun-afd0euia.c-2.us-west-2.aws.neon.tech/neondb?sslmode=require
```

El host es: `ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech`

**Nota:** Usa `-pooler` en el host para mejor rendimiento con conexiones pooling.

### Paso 3: Commit y Push

Abre tu terminal en la carpeta del proyecto y ejecuta:

```bash
git add .
git commit -m "Fix: Configuración correcta de conexión a Neon PostgreSQL"
git push origin main
```

### Paso 4: Esperar el Despliegue

1. Ve a tu servicio en Render
2. Verás que automáticamente inicia un nuevo deploy
3. Observa los logs durante el deploy
4. Deberías ver mensajes como:
   ```
   Iniciando Apache con las siguientes configuraciones:
   Puerto: 10000
   DB Host: ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech
   DB Name: neondb
   SSL Mode: require
   ```

### Paso 5: Verificar la Conexión

Una vez que el deploy termine (status: "Live"), verifica la conexión:

#### Opción A: Diagnóstico Rápido
```
https://testlink-web.onrender.com/dbcheck.php
```
Deberías ver: **✓ Conexión exitosa!**

#### Opción B: Diagnóstico Completo
```
https://testlink-web.onrender.com/test_db_connection.php
```
Verás información detallada sobre la conexión.

#### Opción C: Acceso a TestLink
```
https://testlink-web.onrender.com
```
Deberías ver la página de login de TestLink sin errores.

---

## 🐛 Troubleshooting

### Error: "Variables de entorno no están definidas"

**Causa:** Las variables no están configuradas en Render

**Solución:**
1. Ve a Render → tu servicio → Environment
2. Agrega todas las variables listadas en el Paso 1
3. Click en "Save Changes"
4. Render redesplegará automáticamente

---

### Error: "could not connect to server" o "timeout"

**Causa:** El host de la base de datos es incorrecto

**Solución:**
1. Ve a tu dashboard de Neon
2. Copia el connection string exacto
3. Extrae el host (sin `postgresql://` y sin la parte después de `.tech`)
4. Actualiza `TL_DB_HOST` en Render
5. Si es diferente al que tienes en `render.yaml` línea 15, actualiza ambos lugares

---

### Error: "SSL connection required"

**Causa:** Variables SSL no configuradas

**Solución:**
1. Verifica que `PGSSLMODE=require` esté en las variables de entorno de Render
2. Verifica que `PGOPTIONS=endpoint=ep-silent-sun-afd0euia` esté configurado
3. Redespliega

---

### Error: "password authentication failed"

**Causa:** Password incorrecta

**Solución:**
1. Ve a Neon → tu proyecto → Settings
2. Reset password si es necesario
3. Actualiza `TL_DB_PASS` en Render
4. Redespliega

---

### La página carga pero sigue mostrando "Database connection failed"

**Posibles causas y soluciones:**

1. **Cache del navegador:** 
   - Haz Ctrl+Shift+R (force refresh)
   - Prueba en ventana incógnita

2. **Variables no se pasan a PHP:**
   - Ve a Render → Logs
   - Busca los mensajes de "Iniciando Apache"
   - Verifica que las variables se muestren correctamente

3. **ADODB no puede conectar:**
   - Accede a `dbcheck.php` - si funciona, el problema está en ADODB
   - Revisa los logs de Render para errores de PHP
   - Los errores aparecerán en formato: `[error] ... PHP Fatal error`

---

## 📊 Verificación de Éxito

Tu conexión está funcionando correctamente si:

✅ `dbcheck.php` muestra "✓ Conexión exitosa!"  
✅ `test_db_connection.php` muestra conexión OK con pg_connect y ADODB  
✅ TestLink carga sin error "Database connection failed"  
✅ Puedes hacer login en TestLink  

---

## 🔍 Información Técnica

### ¿Por qué estos cambios?

1. **DB_TYPE cambió a 'postgres8':**
   - TestLink 1.9.0 usa ADODB 5.x
   - El driver 'postgres8' es más estable para SSL
   - Mejor soporte para opciones de conexión especiales

2. **apache-env.conf:**
   - Apache por defecto no pasa variables de entorno a PHP
   - `PassEnv` hace que las variables estén disponibles en `getenv()` de PHP

3. **Validaciones en run.sh:**
   - Detecta problemas antes de que Apache inicie
   - Facilita debug viendo los logs de Render

4. **Configuración de SSL:**
   - Neon requiere SSL obligatorio
   - El parámetro `endpoint` es específico de Neon para routing correcto

### Formato de Conexión Neon

Neon usa un formato especial:
```
Host: <endpoint>-pooler.<region>.aws.neon.tech
Port: 5432
SSL: required
Options: endpoint=<endpoint-sin-pooler>
```

---

## 📞 Soporte Adicional

Si después de seguir estos pasos el problema persiste:

1. **Revisa los logs de Render:**
   - Render Dashboard → tu servicio → Logs
   - Busca líneas con "ERROR" o "FATAL"
   - Copia el error exacto

2. **Verifica la configuración de Neon:**
   - Neon Dashboard → tu proyecto → Connection Details
   - Asegúrate de que la base de datos esté activa
   - Verifica que no haya límites de conexión alcanzados

3. **Archivos de diagnóstico:**
   - `dbcheck.php` - test directo con pg_connect
   - `test_db_connection.php` - test completo con detalles
   - Estos archivos te dirán exactamente dónde está fallando

---

## 🎯 Próximos Pasos Después de Conectar

Una vez que la conexión funcione:

1. **Elimina archivos de diagnóstico** (opcional, por seguridad):
   ```bash
   rm dbcheck.php test_db_connection.php
   ```

2. **Verifica que tu base de datos tenga las tablas de TestLink:**
   - Si migraste la base de datos, verifica que todas las tablas estén presentes
   - Si es una base nueva, ejecuta el instalador de TestLink

3. **Configura backup:**
   - Neon tiene backups automáticos
   - Pero considera exports periódicos adicionales

---

## ✨ Resumen de Comandos

```bash
# 1. Ver los cambios
git status

# 2. Commit y push
git add .
git commit -m "Fix: Configuración correcta de conexión a Neon PostgreSQL"
git push origin main

# 3. Verificar en navegador (después del deploy)
# https://testlink-web.onrender.com/dbcheck.php
# https://testlink-web.onrender.com
```

---

**¡Listo!** Con estos cambios, tu TestLink debería conectarse correctamente a Neon PostgreSQL. 🎉
