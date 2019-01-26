<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container = new \App\Kernel\Container();

try {
    $container->start();
} catch (\Exception $e) {
    return $e;
}
