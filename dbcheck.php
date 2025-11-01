<?php
echo "<h2>TestLink - Diagnóstico de Conexión Neon PostgreSQL</h2>";

$host     = getenv('TL_DB_HOST');
$port     = getenv('TL_DB_PORT') ?: '5432';
$dbname   = getenv('TL_DB_NAME');
$user     = getenv('TL_DB_USER');
$pass     = getenv('TL_DB_PASS');

echo "<p><strong>Variables de entorno:</strong></p>";
echo "Host: $host<br>";
echo "Port: $port<br>";
echo "Database: $dbname<br>";
echo "User: $user<br>";
echo "Password: " . (empty($pass) ? 'NO DEFINIDA' : '*** (definida)') . "<br>";
echo "PGSSLMODE: " . getenv('PGSSLMODE') . "<br>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "Extensión pgsql: " . (extension_loaded('pgsql') ? 'Cargada' : 'NO Cargada') . "<br>";

echo "<p><strong>Intentando conexión (método 1 - sin opciones endpoint)...</strong></p>";

// Método 1: Sin opciones de endpoint (PHP 5.6 no las soporta bien)
$conn_str = "host=$host port=$port dbname=$dbname user=$user password=$pass sslmode=require";

echo "<p style='font-size:12px'>Connection string: " . str_replace($pass, '***', $conn_str) . "</p>";

$c = @pg_connect($conn_str);
if(!$c){ 
    $error = pg_last_error();
    if (empty($error)) {
        $error = "No se pudo establecer conexión (libpq no retornó error específico)";
    }
    echo "<p style='color:red'><strong>ERROR método 1:</strong> $error</p>"; 
    
    // Método 2: Intentar sin sslmode
    echo "<p><strong>Intentando conexión (método 2 - sin SSL explícito)...</strong></p>";
    $conn_str2 = "host=$host port=$port dbname=$dbname user=$user password=$pass";
    $c = @pg_connect($conn_str2);
    
    if (!$c) {
        echo "<p style='color:red'><strong>ERROR método 2:</strong> " . pg_last_error() . "</p>";
        echo "<hr>";
        echo "<p><strong>DIAGNÓSTICO:</strong></p>";
        echo "<ul>";
        echo "<li>PHP 5.6 con libpq antiguo puede no soportar correctamente Neon</li>";
        echo "<li>Verifica que el host sea el correcto en Neon dashboard</li>";
        echo "<li>Verifica que la password sea correcta</li>";
        echo "<li>Considera actualizar a PHP 7.x o superior</li>";
        echo "</ul>";
        exit;
    }
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
echo "<p style='color:green'><strong>✅ Todo OK! La conexión funciona.</strong></p>";
