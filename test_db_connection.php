<?php
echo "<h2>Diagnóstico de Conexión a Base de Datos</h2>";

// Mostrar variables de entorno
echo "<h3>1. Variables de entorno:</h3>";
$vars = ['TL_DB_HOST', 'TL_DB_PORT', 'TL_DB_NAME', 'TL_DB_USER', 'TL_DB_PASS', 'PGSSLMODE', 'PGOPTIONS'];
foreach ($vars as $var) {
    $value = getenv($var);
    echo "$var: " . ($var == 'TL_DB_PASS' ? '***' : ($value ?: 'NO DEFINIDA')) . "<br>";
}

// Cargar configuración
echo "<h3>2. Cargando config_db.inc.php...</h3>";
require_once('config_db.inc.php');

echo "DB_TYPE: " . DB_TYPE . "<br>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_PASS: " . (DB_PASS ? '*** (definida)' : 'NO DEFINIDA') . "<br>";

// Intentar conexión usando pg_connect directamente
echo "<h3>3. Prueba con pg_connect nativo:</h3>";

$db_host_parts = explode(':', DB_HOST);
$host = $db_host_parts[0];
$port = isset($db_host_parts[1]) ? $db_host_parts[1] : '5432';
$endpoint = getenv('PGOPTIONS') ?: 'endpoint=ep-silent-sun-afd0euia';

// Limpiar el endpoint si tiene el formato completo
$endpoint_value = str_replace('endpoint=', '', $endpoint);

$conn_str = sprintf(
    "host=%s port=%s dbname=%s user=%s password=%s sslmode=require options='endpoint=%s'",
    $host, $port, DB_NAME, DB_USER, DB_PASS, $endpoint_value
);

echo "String de conexión: " . str_replace(DB_PASS, '***', $conn_str) . "<br><br>";

$conn = @pg_connect($conn_str);
if ($conn) {
    echo "<strong style='color:green'>✓ Conexión exitosa con pg_connect!</strong><br>";
    $result = pg_query($conn, "SELECT version()");
    if ($result) {
        echo "Versión PostgreSQL: " . pg_fetch_result($result, 0, 0) . "<br>";
    }
    pg_close($conn);
} else {
    echo "<strong style='color:red'>✗ Error de conexión:</strong><br>";
    echo pg_last_error() . "<br>";
}

// Intentar con ADODB
echo "<h3>4. Prueba con ADODB (como TestLink):</h3>";
require_once(dirname(__FILE__) . '/third_party/adodb/adodb.inc.php');

$db = NewADOConnection('postgres8');
$db->debug = false;

// Configurar PGSSLMODE antes de conectar
putenv('PGSSLMODE=require');
putenv('PGOPTIONS=' . $endpoint);

$result = @$db->Connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($result) {
    echo "<strong style='color:green'>✓ Conexión exitosa con ADODB!</strong><br>";
    $rs = $db->Execute("SELECT version()");
    if ($rs) {
        echo "Versión PostgreSQL: " . $rs->fields[0] . "<br>";
    }
} else {
    echo "<strong style='color:red'>✗ Error de conexión con ADODB:</strong><br>";
    echo "Error: " . $db->ErrorMsg() . "<br>";
}

echo "<h3>5. Información del servidor PHP:</h3>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "Extensión pgsql: " . (extension_loaded('pgsql') ? 'Cargada' : 'NO Cargada') . "<br>";
echo "Extensión pdo_pgsql: " . (extension_loaded('pdo_pgsql') ? 'Cargada' : 'NO Cargada') . "<br>";
?>
