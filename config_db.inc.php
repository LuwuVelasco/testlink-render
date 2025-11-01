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
define('DB_HOST','ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech'); // o sin -pooler
define('DB_NAME','neondb');
define('DB_USER','neondb_owner');
define('DB_PASS','npg_80WARUtcIvxF');
define('DB_CHARSET', 'UTF-8');
?>
