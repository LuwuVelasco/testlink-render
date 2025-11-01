<?php
// Reemplaza por tus credenciales reales
$host   = 'ep-silent-sun-afd0euia.c-2.us-west-2.aws.neon.tech'; // o ...-pooler...
$dbname = 'neondb';
$user   = 'neondb_owner';
$pass   = 'TU_PASS_MD5';

// 1) Opción A: pasar options + sslmode dentro del string
$dsn = "host={$host} port=5432 dbname={$dbname} user={$user} password={$pass} " .
       "sslmode=require options=endpoint%3Dep-silent-sun-afd0euia";

$conn = pg_connect($dsn);
if (!$conn) {
    die('ERROR --> ' . pg_last_error());
}
echo "OK conectado";
