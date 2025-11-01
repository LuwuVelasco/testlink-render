<?php
/**
 * Fix para conexión ADODB a Neon PostgreSQL
 * Este archivo configura la conexión para usar las opciones de Neon correctamente
 */

// Hook para modificar la conexión ADODB antes de conectar
if (!function_exists('testlink_db_pre_connect')) {
    function testlink_db_pre_connect($db) {
        // Configurar variables de entorno de PostgreSQL
        if (getenv('PGSSLMODE')) {
            putenv('PGSSLMODE=' . getenv('PGSSLMODE'));
        }
        
        if (getenv('PGOPTIONS')) {
            $options = getenv('PGOPTIONS');
            // Asegurar formato correcto
            if (strpos($options, 'endpoint=') !== 0 && !empty($options)) {
                $options = 'endpoint=' . $options;
            }
            putenv('PGOPTIONS=' . $options);
        }
        
        // Para ADODB, necesitamos configurar el driver de PostgreSQL
        if ($db && isset($db->databaseType) && strpos($db->databaseType, 'postgres') !== false) {
            // Configuraciones adicionales para PostgreSQL
            $db->hasTransactions = true;
            $db->hasInsertID = true;
            
            // Si el host no incluye puerto, ADODB usa el puerto por defecto 5432
            // que es lo que queremos para Neon
        }
        
        return $db;
    }
}

// Verificar que las extensiones necesarias estén cargadas
if (!extension_loaded('pgsql')) {
    die('ERROR: Extensión PHP pgsql no está cargada. Necesaria para conectar a PostgreSQL.');
}

// Log de debug (solo si está habilitado)
if (defined('DB_DEBUG') && DB_DEBUG) {
    error_log('DB Neon Fix: PGSSLMODE=' . getenv('PGSSLMODE'));
    error_log('DB Neon Fix: PGOPTIONS=' . getenv('PGOPTIONS'));
    error_log('DB Neon Fix: DB_HOST=' . (defined('DB_HOST') ? DB_HOST : 'undefined'));
}
?>
