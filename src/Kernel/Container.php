<?php

namespace App\Kernel;

use App\Kernel\Exception\HttpException;
use App\Kernel\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

class Container
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Router
     */
    private $router;

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function start() {
        $this->getRouter();
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig() : \Twig_Environment
    {
        if (null === $this->twig) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/../templates');

            $this->twig = new \Twig_Environment($loader, [
                'cache' => false
            ]);

           // $twig->addGlobal('is_test', 'jkhkhj');
        }

        return $this->twig;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration() : Configuration
    {
        if (null === $this->config) {
            $this->config = new Configuration();
        }

        return $this->config;
    }

    /**
     * @return Router
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getRouter() : Router
    {
        if (null === $this->router) {
            try {
                $this->router = new Router($this);
                $this->router->handleRoutes();
            } catch (\Exception $e) {
                if ($e instanceof HttpException) {
                    $response = new Response(
                        $e->getMessage(),
                        $e->getStatusCode(),
                        ['content-type' => 'text/html']
                    );

                    $response->setContent(
                        $this->getTwig()->render('_errors/error.html.twig', ['name' => 'Fabien'])
                    );


                    $response->send();
                }

                throw $e;
            }
        }

        return $this->router;
    }
}
