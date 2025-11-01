<?php
echo "<h2>TestLink - Diagnóstico de Conexión Neon PostgreSQL (PHP 7.4)</h2>";

$host     = getenv('TL_DB_HOST');
$port     = getenv('TL_DB_PORT') ?: '5432';
$dbname   = getenv('TL_DB_NAME');
$user     = getenv('TL_DB_USER');
$pass     = getenv('TL_DB_PASS');
$pgoptions = getenv('PGOPTIONS') ?: '';

echo "<p><strong>Variables de entorno:</strong></p>";
echo "Host: $host<br>";
echo "Port: $port<br>";
echo "Database: $dbname<br>";
echo "User: $user<br>";
echo "Password: " . (empty($pass) ? 'NO DEFINIDA' : '*** (definida)') . "<br>";
echo "PGSSLMODE: " . getenv('PGSSLMODE') . "<br>";
echo "PGOPTIONS: " . ($pgoptions ?: 'no definido') . "<br>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "Extensión pgsql: " . (extension_loaded('pgsql') ? 'Cargada' : 'NO Cargada') . "<br>";

// Método 1: Con PGOPTIONS (PHP 7.4)
echo "<p><strong>Intentando conexión (método 1 - con PGOPTIONS para Neon)...</strong></p>";

// Limpiar endpoint si viene con 'endpoint='
$endpoint = str_replace('endpoint=', '', $pgoptions);

$conn_str = "host=$host port=$port dbname=$dbname user=$user password=$pass sslmode=require";
if (!empty($endpoint)) {
    $conn_str .= " options='-c search_path=public'";
}

echo "<p style='font-size:12px'>Connection string: " . str_replace($pass, '***', $conn_str) . "</p>";

// Configurar variables de entorno antes de conectar
putenv("PGSSLMODE=require");
if (!empty($endpoint)) {
    putenv("PGOPTIONS=-c endpoint=$endpoint");
}

$c = @pg_connect($conn_str);

if(!$c){ 
    $error = error_get_last();
    echo "<p style='color:red'><strong>ERROR método 1:</strong></p>";
    if ($error) {
        echo "<pre style='color:red'>" . print_r($error, true) . "</pre>";
    }
    
    // Método 2: Connection string alternativo para Neon
    echo "<p><strong>Intentando conexión (método 2 - formato alternativo)...</strong></p>";
    
    // Para Neon, a veces funciona mejor sin especificar SSL en string
    $conn_str2 = "host=$host port=$port dbname=$dbname user=$user password=$pass";
    $c = @pg_connect($conn_str2);
    
    if (!$c) {
        $error2 = error_get_last();
        echo "<p style='color:red'><strong>ERROR método 2:</strong></p>";
        if ($error2) {
            echo "<pre style='color:red'>" . print_r($error2, true) . "</pre>";
        }
        
        echo "<hr>";
        echo "<p><strong>DIAGNÓSTICO:</strong></p>";
        echo "<ul>";
        echo "<li>Verifica que el host sea el correcto en Neon dashboard</li>";
        echo "<li>Verifica que la password sea correcta</li>";
        echo "<li>El host debe ser el que termina en -pooler.c-2.us-west-2.aws.neon.tech</li>";
        echo "<li>Verifica las variables de entorno en Render → Environment</li>";
        echo "</ul>";
        exit;
    } else {
        echo "<p style='color:green'><strong>✓ Método 2 funcionó!</strong></p>";
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
