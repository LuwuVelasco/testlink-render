# 🚀 Guía de Actualización a PHP 7.4

## ⚠️ Por Qué Es Necesario

PHP 5.6 con libpq antiguo **NO PUEDE** conectarse a Neon PostgreSQL. Es una incompatibilidad fundamental entre:
- libpq de 2014 (PHP 5.6) 
- Neon PostgreSQL moderno (2021+)

**No hay forma de hacerlos compatibles sin actualizar PHP.**

---

## ✅ Solución: PHP 7.4

PHP 7.4 incluye libpq moderno que funciona perfectamente con Neon, y es lo suficientemente antiguo para ejecutar TestLink 1.9.0 sin problemas mayores.

---

## 📋 PASOS PARA ACTUALIZAR

### Paso 1: Backup de Archivos Actuales

```powershell
# Opcional: Crear backup de archivos actuales
copy Dockerfile Dockerfile.php56.backup
copy config_db.inc.php config_db.inc.php56.backup
```

### Paso 2: Reemplazar Archivos

```powershell
# Reemplazar Dockerfile
copy Dockerfile.php74 Dockerfile

# Reemplazar config_db
copy config_db.inc.php74 config_db.inc.php
```

**O manualmente:**

#### A. Actualizar Dockerfile

Cambiar línea 2:
```dockerfile
# Antes:
FROM php:5.6-apache

# Después:
FROM php:7.4-apache
```

Actualizar instalación de dependencias (líneas 5-20):
```dockerfile
RUN set -eux; \
  apt-get update; \
  DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    ca-certificates curl build-essential \
    libpq-dev libxml2-dev zlib1g-dev \
    libjpeg62-turbo-dev libfreetype6-dev libpng-dev \
    libzip-dev unzip; \
  docker-php-ext-install mbstring xml zip; \
  docker-php-ext-install pgsql pdo_pgsql; \
  docker-php-ext-configure gd --with-freetype --with-jpeg; \
  docker-php-ext-install gd; \
  a2enmod rewrite; \
  rm -rf /var/lib/apt/lists/*
```

#### B. Actualizar config_db.inc.php

Cambiar DB_TYPE (línea ~17):
```php
// Antes:
define('DB_TYPE','postgres7');

// Después:
define('DB_TYPE','postgres9');
```

Cambiar DB_HOST (línea ~33):
```php
// Antes:
define('DB_HOST', $db_host);

// Después:
define('DB_HOST', $db_host . ':' . $db_port);
```

Habilitar PGOPTIONS (línea ~44):
```php
// Agregar:
$pg_options = getenv('PGOPTIONS');
if (!empty($pg_options)) {
    if (strpos($pg_options, 'endpoint=') !== 0) {
        $pg_options = 'endpoint=' . $pg_options;
    }
    putenv('PGOPTIONS=' . $pg_options);
}
```

### Paso 3: Habilitar PGOPTIONS en render.yaml

Descomentar en `render.yaml` (líneas 12-14):
```yaml
# Antes:
# PGOPTIONS deshabilitado - PHP 5.6 con libpq antiguo no lo soporta
# - key: PGOPTIONS
#   value: endpoint=ep-silent-sun-afd0euia

# Después:
- key: PGOPTIONS
  value: endpoint=ep-silent-sun-afd0euia
```

### Paso 4: Actualizar run.sh

Habilitar export de PGOPTIONS (línea ~11):
```bash
# Antes:
# Nota: PGOPTIONS no se usa porque PHP 5.6 con libpq antiguo no lo soporta correctamente
export PGSSLMODE="${PGSSLMODE:-require}"

# Después:
export PGSSLMODE="${PGSSLMODE:-require}"
export PGOPTIONS="${PGOPTIONS:-endpoint=ep-silent-sun-afd0euia}"
```

---

## 🚀 Desplegar Cambios

```powershell
# Ver cambios
git status

# Agregar archivos modificados
git add Dockerfile config_db.inc.php render.yaml run.sh

# Commit
git commit -m "Upgrade: Actualizar a PHP 7.4 para compatibilidad con Neon PostgreSQL"

# Push (esto disparará el deploy automático en Render)
git push origin main
```

---

## ✅ Verificar Después del Deploy

### 1. Monitorear Logs en Render

Durante el deploy, deberías ver:
```
Building...
[+] Building...
FROM php:7.4-apache
...
Successfully built
```

### 2. Verificar Conexión a Base de Datos

Una vez que esté "Live", accede a:
```
https://testlink-web.onrender.com/dbcheck.php
```

Deberías ver:
```
✓ Conexión exitosa!
PostgreSQL Version: PostgreSQL 16.x on x86_64-pc-linux-gnu...
```

### 3. Acceder a TestLink

```
https://testlink-web.onrender.com
```

Deberías ver la página de login sin error de base de datos.

---

## 🐛 Posibles Problemas y Soluciones

### Error: "deprecated functions"

Si ves warnings de funciones deprecated:

1. Son solo warnings, no errores críticos
2. TestLink 1.9.0 puede tener funciones antiguas pero seguirán funcionando
3. Puedes ignorarlos temporalmente

**Solución:** Agregar a `php.ini`:
```ini
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
```

Ya está configurado en el Dockerfile.

---

### Error: "session functions changed"

TestLink puede usar funciones de sesión antiguas.

**Solución:** Ya está manejado en `custom_config.inc.php` (líneas 14-19)

---

### Error: "continue targeting switch is equivalent to break"

Esto es un cambio de sintaxis en PHP 7.3+.

**Solución:** Solo afecta si hay código con `continue` dentro de `switch`. TestLink 1.9.0 generalmente no tiene este problema.

---

### TestLink carga pero muestra errores PHP

Si ves errores PHP pero TestLink funciona parcialmente:

1. Anota los errores específicos
2. Busca en los archivos de TestLink la línea problemática
3. Generalmente son warnings que no afectan funcionalidad

---

## 📊 Comparación Antes/Después

| Aspecto | PHP 5.6 | PHP 7.4 |
|---------|---------|---------|
| **Conexión a Neon** | ❌ NO funciona | ✅ Funciona |
| **libpq** | 2014 (antigua) | 2019+ (moderna) |
| **Soporte SSL** | Limitado | Completo |
| **Rendimiento** | Baseline | 2-3x más rápido |
| **Memoria** | Baseline | 30% menos uso |
| **Seguridad** | EOL 2019 | EOL 2022 |

---

## ⏱️ Tiempo Estimado de Actualización

- **Hacer cambios:** 5-10 minutos
- **Build en Render:** 5-10 minutos  
- **Pruebas:** 5-10 minutos

**Total: ~20-30 minutos**

---

## 💾 Rollback (Si es necesario)

Si algo sale mal y necesitas volver a PHP 5.6:

```powershell
# Restaurar archivos
copy Dockerfile.php56.backup Dockerfile
copy config_db.inc.php56.backup config_db.inc.php

# O revertir commit
git revert HEAD

# Push
git push origin main
```

**Nota:** Con PHP 5.6 no podrás usar Neon, tendrás que volver a PostgreSQL de Render.

---

## 🎯 Resumen Ejecutivo

1. **Cambiar:** `FROM php:5.6-apache` → `FROM php:7.4-apache`
2. **Actualizar:** `DB_TYPE` a `postgres9`
3. **Habilitar:** `PGOPTIONS` en render.yaml y run.sh
4. **Desplegar:** `git push`
5. **Verificar:** dbcheck.php

**¿Listo para actualizar?**
