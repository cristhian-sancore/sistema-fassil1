<?php
//------------------- CONFIGURACOES DO BANCO
define('HOST', getenv('DB_HOST') ?: 'db');
define('USER', getenv('DB_USER') ?: 'grupofas_admin');
define('PASS', getenv('DB_PASS') ?: 'fassil3017#');
define('DBSA', getenv('DB_NAME') ?: 'grupofas_sistema');