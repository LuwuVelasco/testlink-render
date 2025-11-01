<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Fuerza SSL y pasa el endpoint a libpq (tu libpq no soporta SNI) */
putenv('PGSSLMODE=require');
putenv('PGOPTIONS=endpoint=ep-silent-sun-afd0euia');  // <-- SIN %3D

$host   = 'ep-silent-sun-afd0euia.c-2.us-west-2.aws.neon.tech'; // puedes usar el -pooler también
$dbname = 'neondb';
$user   = 'neondb_owner';
$pass   = 'TU_PASSWORD_MD5'; // la contraseña que ya convertiste a md5 en Neon

// IMPORTANTE: nada de URL encoding aquí
$dsn = "host={$host} port=5432 dbname={$dbname} user={$user} password={$pass} ".
       "sslmode=require options=endpoint=ep-silent-sun-afd0euia";

$conn = pg_connect($dsn);
if (!$conn) {
  die('ERROR --> ' . pg_last_error());
}

$r = pg_query($conn, "select version(), current_user, current_database()");
var_dump(pg_fetch_row($r));
