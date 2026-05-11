<?php

define('ROOT_PATH',       __DIR__ . '/');
define('CONTROLLER_PATH', __DIR__ . '/CONTROLLER/');
define('MODEL_PATH',      __DIR__ . '/MODEL/');
define('VIEW_PATH',       __DIR__ . '/VIEW/');

$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'];
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . '://' . $host . $scriptDir . '/');

define('ASSETS_URL', BASE_URL . 'assets/');
define('CSS_URL',    ASSETS_URL . 'css/');
define('JS_URL',     ASSETS_URL . 'js/');
define('IMG_URL',    ASSETS_URL . 'images/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id']     = 2;
    $_SESSION['user_nom']    = 'Ben Ali';
    $_SESSION['user_prenom'] = 'Mohamed';
}
?>