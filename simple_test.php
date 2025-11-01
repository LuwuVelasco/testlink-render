<?php
// Test simple de conexión a Neon PostgreSQL
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test Simple de Conexión a Neon</h2>";

// Obtener credenciales
$host = getenv('TL_DB_HOST');
$port = getenv('TL_DB_PORT') ?: '5432';
$dbname = getenv('TL_DB_NAME');
$user = getenv('TL_DB_USER');
$pass = getenv('TL_DB_PASS');

echo "<h3>1. Credenciales Recibidas:</h3>";
echo "Host: <code>$host</code><br>";
echo "Port: <code>$port</code><br>";
echo "Database: <code>$dbname</code><br>";
echo "User: <code>$user</code><br>";
echo "Password: <code>" . (empty($pass) ? 'VACÍA!' : substr($pass, 0, 3) . '...') . "</code><br>";

// Verificar extensión
echo "<h3>2. Extensiones PHP:</h3>";
echo "pgsql: " . (extension_loaded('pgsql') ? '✅ Cargada' : '❌ NO cargada') . "<br>";
echo "pdo_pgsql: " . (extension_loaded('pdo_pgsql') ? '✅ Cargada' : '❌ NO cargada') . "<br>";
echo "PHP version: " . phpversion() . "<br>";

// Obtener endpoint para Neon
$pgoptions = getenv('PGOPTIONS') ?: '';
$endpoint = str_replace('endpoint=', '', $pgoptions);

echo "PGOPTIONS: <code>$pgoptions</code><br>";
echo "Endpoint: <code>$endpoint</code><br>";

// Test 1: PDO (más moderno)
echo "<h3>3. Test con PDO:</h3>";
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    if (!empty($endpoint)) {
        $dsn .= ";options='endpoint=$endpoint'";
    }
    echo "DSN: <code>$dsn</code><br>";
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    echo "<p style='color:green;font-weight:bold'>✅ PDO: Conexión EXITOSA!</p>";
    
    $stmt = $pdo->query("SELECT version(), current_database(), current_user");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    echo "<strong>PostgreSQL Version:</strong> " . $row[0] . "<br>";
    echo "<strong>Database:</strong> " . $row[1] . "<br>";
    echo "<strong>User:</strong> " . $row[2] . "<br>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;font-weight:bold'>❌ PDO FALLÓ:</p>";
    echo "<pre style='color:red'>" . $e->getMessage() . "</pre>";
}

// Test 2: pg_connect (legacy)
echo "<h3>4. Test con pg_connect:</h3>";
$conn_str = "host=$host port=$port dbname=$dbname user=$user password=$pass sslmode=require";
if (!empty($endpoint)) {
    $conn_str .= " options='endpoint=$endpoint'";
}
echo "Connection string: <code>" . str_replace($pass, '***', $conn_str) . "</code><br>";

$conn = @pg_connect($conn_str);
if ($conn) {
    echo "<p style='color:green;font-weight:bold'>✅ pg_connect: Conexión EXITOSA!</p>";
    
    $result = pg_query($conn, "SELECT version()");
    if ($result) {
        $row = pg_fetch_row($result);
        echo "<strong>Version:</strong> " . $row[0] . "<br>";
    }
    pg_close($conn);
} else {
    echo "<p style='color:red;font-weight:bold'>❌ pg_connect FALLÓ</p>";
    $errors = error_get_last();
    if ($errors) {
        echo "<pre style='color:red'>" . print_r($errors, true) . "</pre>";
    }
}

echo "<hr>";
echo "<h3>Diagnóstico:</h3>";
if ($conn || isset($pdo)) {
    echo "<p style='color:green'>✅ <strong>La conexión funciona!</strong> El problema debe estar en la configuración de ADODB en TestLink.</p>";
} else {
    echo "<p style='color:red'>❌ <strong>La conexión falla.</strong> Revisa:</p>";
    echo "<ul>";
    echo "<li>¿El host es correcto? Debe terminar en <code>-pooler.c-2.us-west-2.aws.neon.tech</code></li>";
    echo "<li>¿El password es correcto? Cópialo directamente del panel de Neon</li>";
    echo "<li>¿La base de datos existe? Verifica en Neon dashboard</li>";
    echo "</ul>";
}
?>
