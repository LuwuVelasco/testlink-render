<?php
// Cargar helper para conexión Neon si existe
$neon_fix = dirname(__FILE__) . '/lib/functions/db_neon_fix.php';
if (file_exists($neon_fix)) {
    require_once($neon_fix);
}

/* Conexión a PostgreSQL en Render
define('DB_TYPE','pgsql');
define('DB_HOST','dpg-d3g0q9re5dus73ahiavg-a:5432'); // solo host:puerto, sin "postgresql://"
define('DB_NAME','testlink_db');
define('DB_USER','testlink_db_user');
define('DB_PASS','DeNQsKmanqf3LSbzo4TwO40wy0UbWyHf');
define('DB_TABLE_PREFIX',''); // usualmente vacío*/

// Conexión a PostgreSQL en Neon con PHP 7.4
define('DB_TYPE','postgres8'); // Usar postgres8 (postgres9 no existe en TestLink 1.9.0)

// Obtener variables de entorno
$db_host = getenv('TL_DB_HOST');
$db_port = getenv('TL_DB_PORT') ?: '5432';
$db_name = getenv('TL_DB_NAME');
$db_user = getenv('TL_DB_USER');
$db_pass = getenv('TL_DB_PASS');

// Validar que todas las variables estén definidas
if (empty($db_host) || empty($db_name) || empty($db_user) || empty($db_pass)) {
    die('ERROR: Variables de entorno de base de datos no están definidas correctamente');
}

// Para Neon, necesitamos construir el connection string con opciones especiales
// Obtener endpoint
$pg_options = getenv('PGOPTIONS') ?: '';
$endpoint = str_replace('endpoint=', '', $pg_options);

// Definir constantes para TestLink
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_TABLE_PREFIX','');
define('DB_CHARSET','UTF-8');

// Para ADODB con Neon, usar connection string completo con endpoint
// ADODB postgres8 soporta opciones en el host
if (!empty($endpoint)) {
    // Formato: host=xxx port=xxx options='endpoint=yyy'
    define('DB_HOST', "host=$db_host port=$db_port options='endpoint=$endpoint' sslmode=require");
} else {
    // Fallback sin endpoint
    define('DB_HOST', $db_host . ':' . $db_port);
}

// Configurar SSL para Neon en variables de entorno (para funciones pg_* nativas)
putenv('PGSSLMODE=require');
if (!empty($endpoint)) {
    putenv('PGOPTIONS=endpoint=' . $endpoint);
}

// DSN para ADODB (no usado por defecto pero disponible)
define('DSN', false);
?>
