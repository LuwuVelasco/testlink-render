<?php
// Idioma y zona horaria
$tlCfg->default_language = 'es_ES';
date_default_timezone_set('America/La_Paz');

// Desactivar autorregistro
$tlCfg->user_self_signup = FALSE;

// Repositorio de adjuntos en la BD (Render Free no persiste disco entre deploys)
$g_repositoryType = TL_REPOSITORY_TYPE_DB;
$tlCfg->repository_max_filesize = 8; // MB

// Ocultar banner de chequeos (los escribe en archivo si config falla)
$tlCfg->config_check_warning_mode = 'SILENT';

// HTTPS detrás de proxy (Render) sin tocar la sesión manualmente
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// Parámetros de sesión (NO llamar session_start() aquí)
ini_set('session.use_only_cookies','1');
ini_set('session.cookie_httponly','1');
ini_set('session.cookie_samesite','Lax');
ini_set('session.gc_maxlifetime','54000');
ini_set('session.cache_expire','900');
ini_set('session.save_path','/tmp');
putenv('PGSSLMODE=require');
putenv('PGOPTIONS=endpoint=ep-silent-sun-afd0euia');