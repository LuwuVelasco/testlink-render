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

// Conexión a PostgreSQL en Neon
define('DB_TYPE','postgres8'); // Usar postgres8 en lugar de pgsql para ADODB

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

// Definir constantes para TestLink
define('DB_HOST', $db_host . ':' . $db_port);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_TABLE_PREFIX','');
define('DB_CHARSET','UTF-8');

// Configurar SSL y opciones de conexión para Neon
putenv('PGSSLMODE=require');

// Obtener el endpoint de PGOPTIONS (puede venir como "endpoint=xxx" o solo "xxx")
$pg_options = getenv('PGOPTIONS') ?: 'endpoint=ep-silent-sun-afd0euia';
if (strpos($pg_options, 'endpoint=') !== 0) {
    $pg_options = 'endpoint=' . $pg_options;
}
putenv('PGOPTIONS=' . $pg_options);

// DSN alternativo para ADODB (no usado por defecto pero disponible)
define('DSN', false);
?>
