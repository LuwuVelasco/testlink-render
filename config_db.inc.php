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
define('DB_HOST', getenv('TL_DB_HOST'));
define('DB_NAME', getenv('TL_DB_NAME'));
define('DB_USER', getenv('TL_DB_USER'));
define('DB_PASS', getenv('TL_DB_PASS'));
define('DB_TABLE_PREFIX','');
define('DB_CHARSET','UTF-8');

// Fuerza SSL + endpoint para libpq viejo (PHP 5.6)
putenv('PGSSLMODE=require');
putenv('PGOPTIONS=endpoint=ep-silent-sun-afd0euia');
?>
