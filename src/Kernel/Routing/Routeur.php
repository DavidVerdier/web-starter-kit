<?php

namespace App\Kernel\Routing;

use App\Kernel\Exception\MethodNotAllowedHttpException;
use App\Kernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use FastRoute;

class Routeur
{
    private $routes;

    private $dispatcher;

    private $httpMethod;

    private $uri;

    public function __construct()
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($this->uri, '?')) {
            $this->uri = substr($this->uri, 0, $pos);
        }

        $this->uri = rawurldecode($this->uri);
    }

    public function handleRoutes()
    {
        $this->routes = Yaml::parseFile(dirname(__DIR__) . '/../../config/routes.yaml');
        $this->setDispacher();
        $this->buildRoutes();

        return $this;
    }

    private function setDispacher(): Routeur
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            foreach ($this->routes as $route) {
                $method = isset($route["method"]) ? $route["method"] : 'GET';
                $r->addRoute($method, $route["path"], $route["controller"]);
            }
        });

        return $this;
    }

    private function buildRoutes()
    {
        $routeInfo = $this->dispatcher->dispatch($this->httpMethod, $this->uri);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException('Page not found!');
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                throw new MethodNotAllowedHttpException($allowedMethods, 'Method not allowed');
            case FastRoute\Dispatcher::FOUND:

                $data = explode('::', $routeInfo[1]);
                $controller = $data[0];
                $method = $data[1];
                $object =  new $controller();

                return call_user_func_array(array($object, $method), $routeInfo[2]);
        }
    }

    public function getDispacher()
    {
        return $this->dispatcher;
    }
}






