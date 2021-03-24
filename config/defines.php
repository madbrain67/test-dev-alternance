<?php

$protocol = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocol = 'https';
}

if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])) {
    $dossier = explode('/', $_SERVER['SCRIPT_NAME']);
    $strle_dossier = strlen($dossier[1]);
    $delimite = $dossier[1].'/';
    $string = $_SERVER['SCRIPT_NAME'];
    $path_location = substr($string, 0, strpos($string, $delimite) + $strle_dossier);
    define('PATH_LOCATION', $protocol.'://'.$_SERVER['HTTP_HOST'].$path_location);
    define('PATH_BASE_TWIG', $protocol.'://'.$_SERVER['HTTP_HOST']);
    define('BASE', $protocol.'://'.$_SERVER['HTTP_HOST']);
}

define('APP_DEV', true);

define('PATH_ROOT', dirname(__DIR__));
define('APP_VIEW_PATH', PATH_ROOT.'/templates/');
define('APP_CACHE_PATH', PATH_ROOT.'/var/cache/');
define('APP_LOGS_PATH', PATH_ROOT.'/var/logs/');
