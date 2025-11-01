# 🚀 INSTRUCCIONES RÁPIDAS - Solución Conexión TestLink + Neon

## ¿Qué se arregló?

He corregido la configuración de conexión entre TestLink (en Render) y tu base de datos PostgreSQL en Neon.

**Problema encontrado:** El tipo de driver de base de datos y el host tenían configuraciones incorrectas.

---

## ✅ QUÉ HACER AHORA (3 pasos)

### 1️⃣ Verificar Variables de Entorno en Render

**ANTES de hacer push**, ve a https://dashboard.render.com

1. Selecciona tu servicio **testlink-web**
2. Ve a **Environment** (menú lateral)
3. Verifica que estas variables existan:

```
TL_DB_HOST (debe terminar en .c-2.us-west-2.aws.neon.tech)
TL_DB_PORT = 5432
TL_DB_NAME = neondb
TL_DB_USER = neondb_owner
TL_DB_PASS = npg_B0WARUtcIvxF
PGSSLMODE = require
PGOPTIONS = endpoint=ep-silent-sun-afd0euia
TL_TIMEZONE = America/La_Paz
```

**⚠️ MUY IMPORTANTE:**
- El `TL_DB_HOST` ya está configurado correctamente con `c-2` (según tu configuración actual)
- Si tu password es diferente a la mostrada, **usa tu password actual**
- Asegúrate de que **TODAS las variables existan en Render**

---

### 2️⃣ Hacer Push de los Cambios

Abre tu terminal (PowerShell, CMD, o Git Bash) en la carpeta del proyecto:

```powershell
# Ver qué archivos se modificaron
git status

# Agregar todos los cambios
git add .

# Hacer commit
git commit -m "Fix: Configurar conexión a Neon PostgreSQL correctamente"

# Subir a GitHub/GitLab (esto disparará el deploy en Render)
git push origin main
```

**Nota:** Si tu rama se llama diferente (por ejemplo `master`), usa ese nombre en lugar de `main`.

---

### 3️⃣ Esperar y Verificar

1. **Espera 3-5 minutos** mientras Render despliega automáticamente

2. Ve a Render → tu servicio → **Logs** (menú lateral)

3. Busca estas líneas en los logs:
   ```
   Iniciando Apache con las siguientes configuraciones:
   DB Host: ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech
   DB Name: neondb
   ```

4. Cuando el deploy termine (status "Live"), verifica en tu navegador:
   
   **Verificación rápida:**
   ```
   https://testlink-web.onrender.com/dbcheck.php
   ```
   Debe decir: **✓ Conexión exitosa!**

   **Tu aplicación:**
   ```
   https://testlink-web.onrender.com
   ```
   Debe cargar TestLink sin error de base de datos

---

## 🎉 ¡Listo!

Si ves la pantalla de login de TestLink, **¡funcionó!**

---

## ❓ ¿Problemas?

Si después del push algo sale mal:

1. Ve a Render → Logs y busca líneas con "ERROR"
2. Lee el archivo `README_SOLUCION.md` para troubleshooting detallado
3. Usa los archivos de diagnóstico:
   - `dbcheck.php` - verifica conexión básica
   - `test_db_connection.php` - diagnóstico completo

---

## 📝 Archivos Modificados

Para tu referencia, se modificaron:
- ✏️ `config_db.inc.php` - Configuración de base de datos
- ✏️ `render.yaml` - Variables de entorno (corregido host)
- ✏️ `run.sh` - Script de inicio con validaciones
- ✏️ `Dockerfile` - Configuración de Apache para variables de entorno
- ➕ `apache-env.conf` - Nuevo: pasa variables a PHP
- ➕ `lib/functions/db_neon_fix.php` - Nuevo: helper de conexión
- ➕ Archivos de diagnóstico y documentación

---

## 🔑 Lo Más Importante

**El cambio crítico:** Cambié el driver de `pgsql` a `postgres8` para mejor compatibilidad con ADODB y las opciones SSL de Neon.

**¿Por qué falló antes?** ADODB (la librería que usa TestLink) con el driver antiguo no manejaba bien las opciones SSL de Neon.

---

**Cualquier duda, consulta el archivo `README_SOLUCION.md` para instrucciones detalladas.**
