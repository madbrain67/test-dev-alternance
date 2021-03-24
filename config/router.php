<?php

$route = explode('/', ltrim($_SERVER['REQUEST_URI'], '/') ?: 'Home');

/*
 * Test si dans le nom de la class
 * des caractères - ou _ sont présents
 * Permet la contruction d'url comme /mentions-legales
 */
if (isset($route[0])) {
    $rest = str_replace(['-', '_'], ['|', '|'], $route[0]);
    $rest = explode('|', $rest);
    if (count($rest) > 1) {
        $route[0] = '';
        foreach ($rest as $r) {
            $route[0] .= ucfirst($r);
        }
    }
}

/*
 * Test si dans le nom de la method
 * des caractères - ou _ sont présents
 * Permet la contruction d'url comme /mentions-legales
 */
if (isset($route[1])) {
    $rest = str_replace(['-', '_'], ['|', '|'], $route[1]);
    $rest = explode('|', $rest);
    if (count($rest) > 1) {
        $i = 0;
        $route[1] = $rest[0];
        foreach ($rest as $r) {
            if ($i != 0) {
                $route[1] .= ucfirst($r);
            }
            ++$i;
        }
    }
}

/**
 * Class, methods et arguments.
 */
$class = 'App\\Controller\\'.ucfirst($route[0] ?? '').'Controller'::class;
$method = isset($route[1]) ? $route[1] : 'index';
$arguments = array_slice($route, 2);

if (class_exists($class, true)) {
    $class = new $class();
    if (in_array($method, get_class_methods($class))) {
        echo call_user_func_array([$class, $method], $arguments);
    } else {
        echo call_user_func([$class, 'index']);
    }
} else {
    header('HTTP/1.0 404 Not Found');
    include_once '../templates/404.html';
    exit();
}
