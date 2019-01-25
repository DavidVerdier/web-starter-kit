<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel\Routing\Routeur;
use Symfony\Component\HttpFoundation\Response;


$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
$twig = new Twig_Environment($loader, [
    'cache' => false,
]);

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

        $response->setContent(
            $twig->render('_errors/error.html.twig', ['name' => 'Fabien'])
        );


        $response->send();
    }

    throw $e;
}
