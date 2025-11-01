<?php
$host     = getenv('TL_DB_HOST');
$port     = getenv('TL_DB_PORT') ?: '5432';
$dbname   = getenv('TL_DB_NAME');
$user     = getenv('TL_DB_USER');
$pass     = getenv('TL_DB_PASS');
$endpoint = 'ep-silent-sun-afd0euia'; // sin "-pooler"

$conn_str = "host=$host port=$port dbname=$dbname user=$user password=$pass ".
            "sslmode=require options='endpoint=$endpoint'";

$c = @pg_connect($conn_str);
if(!$c){ echo "ERROR --> " . @pg_last_error(); exit; }
$r = pg_query($c,"select version()");
echo "OK: " . pg_fetch_result($r,0,0);
