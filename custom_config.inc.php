<?php
$tlCfg->default_language = 'es_ES';
date_default_timezone_set(getenv('TL_TIMEZONE') ?: 'America/La_Paz');
$tlCfg->user_self_signup = FALSE;
$g_repositoryType = TL_REPOSITORY_TYPE_DB;
$tlCfg->repository_max_filesize = 8;
$tlCfg->config_check_warning_mode = 'SILENT';

if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
  $_SERVER['HTTPS'] = 'on';
  $_SERVER['SERVER_PORT'] = 443;
}

ini_set('session.use_only_cookies','1');
ini_set('session.cookie_httponly','1');
ini_set('session.cookie_samesite','Lax');
ini_set('session.gc_maxlifetime','54000');
ini_set('session.cache_expire','900');
ini_set('session.save_path','/tmp');

// PGSSLMODE y PGOPTIONS ya llegan desde env (render.yaml)