<?php
/* Conexión a PostgreSQL en Render
define('DB_TYPE','pgsql');
define('DB_HOST','dpg-d3g0q9re5dus73ahiavg-a:5432'); // solo host:puerto, sin "postgresql://"
define('DB_NAME','testlink_db');
define('DB_USER','testlink_db_user');
define('DB_PASS','DeNQsKmanqf3LSbzo4TwO40wy0UbWyHf');
define('DB_TABLE_PREFIX',''); // usualmente vacío*/
// Conexión a PostgreSQL en Neon
define('DB_TYPE','pgsql');

$host = getenv('TL_DB_HOST') ?: 'ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech';
$name = getenv('TL_DB_NAME') ?: 'neondb';
$user = getenv('TL_DB_USER') ?: 'neondb_owner';
$pass = getenv('TL_DB_PASS') ?: 'npg_80WARUtcIvxF';

define('DB_HOST', $host);
define('DB_NAME', $name);
define('DB_USER', $user);
define('DB_PASS', $pass);

define('DB_TABLE_PREFIX','');
define('DB_CHARSET','UTF-8');
?>
