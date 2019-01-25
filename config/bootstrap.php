<?php

use App\Kernel\Routing\Routeur;
use Symfony\Component\HttpFoundation\Response;

$config = new \App\Kernel\Configuration();

   // $config->getParameter('env')


$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');

$twig = new Twig_Environment($loader, [
    'cache' => false,
    'global' => array('nn' => 'lkjkl')
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
