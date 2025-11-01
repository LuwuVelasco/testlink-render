# 🔧 Corrección de Errores - PHP 7.4 + Neon

## ❌ Errores Encontrados

1. **ADODB_postgres9.class.php no existe** - TestLink 1.9.0 solo tiene hasta postgres8
2. **Conexión SSL fallando** - Falta configuración de certificados
3. **Warnings de código legacy** - PHP 7.4 es más estricto que PHP 5.6
4. **pg_connect no se establece** - Problema con credenciales o SSL

---

## ✅ Correcciones Aplicadas

### 1. **config_db.inc.php**
- ✅ Cambiado `DB_TYPE` de `postgres9` → `postgres8`
- ✅ postgres8 SÍ existe en ADODB de TestLink 1.9.0

### 2. **Dockerfile**
- ✅ Agregado `libssl-dev` para soporte SSL completo
- ✅ Agregado `update-ca-certificates` para certificados raíz
- ✅ Configurado `error_reporting` para suprimir notices/deprecated
- ✅ Logs de errores en `/var/www/html/logs/php_errors.log`

### 3. **dbcheck.php**
- ✅ Actualizado para usar formato correcto con PGOPTIONS
- ✅ Mejor manejo de errores
- ✅ Diagnóstico más detallado

### 4. **simple_test.php** (NUEVO)
- ✅ Test simple con PDO y pg_connect
- ✅ Muestra credenciales parciales para verificar
- ✅ Diagnóstico claro de qué funciona y qué no

---

## 🚨 PROBLEMA CRÍTICO DETECTADO

El error `"No PostgreSQL link opened yet"` indica que **la conexión no se está estableciendo**.

### Posibles causas:

#### 1. **Password Incorrecta** ⚠️ MÁS PROBABLE

El password en las variables de entorno puede estar mal.

**Solución:**
1. Ve a tu dashboard de Neon: https://console.neon.tech
2. Ve a tu proyecto → Connection Details
3. Copia el password **exactamente como aparece**
4. Ve a Render → tu servicio → Environment
5. Edita `TL_DB_PASS` y pega el password correcto
6. Save Changes (Render redesplegará automáticamente)

#### 2. **Host Incorrecto**

El host debe ser exactamente como aparece en Neon.

**Verifica:**
- En Neon dashboard: Connection Details → Host
- Debe ser: `ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech`
- Si es diferente, actualiza `TL_DB_HOST` en Render

#### 3. **Base de Datos no Existe**

Verifica que la base de datos `neondb` exista en tu proyecto Neon.

---

## 🚀 Pasos para Desplegar las Correcciones

### 1. Commit y Push

```powershell
# Ver cambios
git status

# Agregar archivos
git add Dockerfile config_db.inc.php dbcheck.php simple_test.php

# Commit
git commit -m "Fix: Correcciones PHP 7.4 - postgres8, SSL, error handling"

# Push
git push origin main
```

### 2. **IMPORTANTE: Verificar Password en Render**

**ANTES de esperar al deploy**, ve a Render y verifica:

1. https://dashboard.render.com
2. Selecciona `testlink-web`
3. Ve a **Environment**
4. Busca `TL_DB_PASS`
5. **¿El valor coincide con el password de Neon?**

Si no estás seguro:
1. Ve a Neon dashboard
2. Copia el password de Connection Details
3. Actualiza `TL_DB_PASS` en Render
4. Guarda cambios

### 3. Esperar Deploy (5-10 minutos)

El deploy se iniciará automáticamente.

### 4. Verificar Conexión con simple_test.php

Una vez que esté "Live":

```
https://testlink-web.onrender.com/simple_test.php
```

Este script te mostrará:
- ✅ Las credenciales que recibió (parcialmente)
- ✅ Si PDO puede conectar
- ✅ Si pg_connect puede conectar
- ✅ Mensajes de error detallados

---

## 📊 Interpretación de Resultados

### Escenario 1: simple_test.php muestra "✅ PDO: Conexión EXITOSA"

**Significa:** Las credenciales son correctas, SSL funciona.

**Problema:** ADODB en TestLink no está configurado correctamente.

**Solución:** Revisa `lib/functions/db_neon_fix.php` y asegúrate de que no tiene output que cause "headers already sent".

---

### Escenario 2: simple_test.php muestra "❌ PDO FALLÓ" con error de password

**Significa:** La password es incorrecta.

**Solución:**
1. Copia password exacta de Neon
2. Actualiza `TL_DB_PASS` en Render
3. Espera redeploy

---

### Escenario 3: simple_test.php muestra "❌ PDO FALLÓ" con error de host

**Significa:** El host es incorrecto.

**Solución:**
1. Copia host exacto de Neon (el que termina en -pooler)
2. Actualiza `TL_DB_HOST` en Render
3. Actualiza `render.yaml` línea 15
4. Push cambios

---

### Escenario 4: simple_test.php muestra "❌ PDO FALLÓ" con error de SSL

**Significa:** Problema con certificados SSL.

**Solución:** Los cambios en el Dockerfile (libssl-dev, update-ca-certificates) deberían resolverlo. Espera al deploy.

---

## 🐛 Troubleshooting

### Error: "SQLSTATE[08006] could not connect"

- Problema de red o host incorrecto
- Verifica que el host sea exactamente el de Neon

### Error: "SQLSTATE[08006] password authentication failed"

- Password incorrecta
- Copia password de Neon y actualiza en Render

### Error: "SQLSTATE[08006] database does not exist"

- La base de datos no existe en Neon
- Verifica en Neon dashboard que `neondb` existe

### TestLink carga pero dice "Database connection failed"

Si `simple_test.php` funciona pero TestLink no:
- El problema está en ADODB
- Revisa `/var/www/html/logs/php_errors.log` en los logs de Render
- Puede ser configuración de `postgres8` driver

---

## ✅ Checklist de Verificación

Antes de considerar el problema resuelto:

- [ ] `simple_test.php` muestra "✅ PDO: Conexión EXITOSA"
- [ ] `simple_test.php` muestra "✅ pg_connect: Conexión EXITOSA"
- [ ] `dbcheck.php` muestra "✓ Conexión exitosa!"
- [ ] TestLink carga sin error "Database connection failed"
- [ ] Puedes hacer login en TestLink

---

## 📝 Archivos Modificados en Esta Corrección

1. **Dockerfile** - SSL, certificados, error_reporting
2. **config_db.inc.php** - postgres8 en lugar de postgres9
3. **dbcheck.php** - Mejor diagnóstico
4. **simple_test.php** - NUEVO, test simple

---

## 🎯 Siguiente Paso CRÍTICO

**VERIFICA EL PASSWORD EN RENDER** antes de hacer nada más.

El 90% de los problemas de "No PostgreSQL link opened yet" se deben a password incorrecta.

---

**Después de verificar la password, haz el push y prueba `simple_test.php` 🚀**
