<?php
echo "<h2>TestLink - Diagnóstico de Conexión Neon PostgreSQL</h2>";

$host     = getenv('TL_DB_HOST');
$port     = getenv('TL_DB_PORT') ?: '5432';
$dbname   = getenv('TL_DB_NAME');
$user     = getenv('TL_DB_USER');
$pass     = getenv('TL_DB_PASS');
$endpoint = getenv('PGOPTIONS') ?: 'endpoint=ep-silent-sun-afd0euia';

// Limpiar el endpoint
$endpoint = str_replace('endpoint=', '', $endpoint);

echo "<p><strong>Variables de entorno:</strong></p>";
echo "Host: $host<br>";
echo "Port: $port<br>";
echo "Database: $dbname<br>";
echo "User: $user<br>";
echo "Password: " . (empty($pass) ? 'NO DEFINIDA' : '*** (definida)') . "<br>";
echo "Endpoint: $endpoint<br>";
echo "PGSSLMODE: " . getenv('PGSSLMODE') . "<br>";

$conn_str = "host=$host port=$port dbname=$dbname user=$user password=$pass ".
            "sslmode=require options='endpoint=$endpoint'";

echo "<p><strong>Intentando conexión...</strong></p>";

$c = @pg_connect($conn_str);
if(!$c){ 
    echo "<p style='color:red'><strong>ERROR:</strong> " . pg_last_error() . "</p>"; 
    echo "<p>Conexión string (sin password): host=$host port=$port dbname=$dbname user=$user sslmode=require options='endpoint=$endpoint'</p>";
    exit; 
}

echo "<p style='color:green'><strong>✓ Conexión exitosa!</strong></p>";

$r = pg_query($c,"SELECT version()");
if ($r) {
    echo "<p><strong>PostgreSQL Version:</strong> " . pg_fetch_result($r,0,0) . "</p>";
}

// Test adicional
$r2 = pg_query($c,"SELECT current_database(), current_user");
if ($r2) {
    $row = pg_fetch_row($r2);
    echo "<p><strong>Base de datos actual:</strong> {$row[0]}</p>";
    echo "<p><strong>Usuario actual:</strong> {$row[1]}</p>";
}

pg_close($c);
echo "<p style='color:green'><strong>Todo OK!</strong></p>";
