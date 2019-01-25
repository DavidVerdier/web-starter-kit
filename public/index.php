<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel\Routing\Routeur;
use Symfony\Component\HttpFoundation\Response;

try {
    $router = new Routeur();
    $router->handleRoutes();
} catch (\Exception $e) {
    if ($e instanceof \App\Kernel\Exception\HttpException) {
        $response = new Response(
            $e->getMessage(),
            $e->getStatusCode(),
            ['content-type' => 'text/html']
        );

        $response->send();
    }

    throw $e;
}
