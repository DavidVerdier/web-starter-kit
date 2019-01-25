<?php

namespace App\Kernel\Routing;

use App\Kernel\Container;
use App\Kernel\Exception\MethodNotAllowedHttpException;
use App\Kernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use FastRoute;

class Router
{
    private $routes;

    private $dispatcher;

    private $httpMethod;

    private $uri;

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;

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

    private function setDispacher(): Router
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
                $object =  new $controller($this->container);

                return call_user_func_array(array($object, $method), $routeInfo[2]);
        }
    }

    public function getDispacher()
    {
        return $this->dispatcher;
    }

    public function generate(string $routeName, array $parameters): string
    {
        if (!array_key_exists($routeName, $this->routes)) {
            throw new \LogicException("The route $routeName does not exist!");
        }

        $route = $this->routes[$routeName];

        dump($route);die;
    }
}






