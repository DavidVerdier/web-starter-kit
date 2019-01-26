<?php

namespace App\Kernel\Bridges;

use App\Kernel\Container;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Routing
{
    private $fileLocator;

    private $loader;

    private $routes;

    private $context;

    private $matcher;

    private $parameters;

    public function __construct(Container $container)
    {
        $this->fileLocator = new FileLocator([__DIR__ . '/../../../config/']);
        $this->loader = new YamlFileLoader($this->fileLocator);
        $this->routes = $this->loader->load('routes.yaml');

        $this->context = new RequestContext('/');
        $this->context->fromRequest($container->getRequest());

        $this->matcher = new UrlMatcher($this->routes, $this->context);

        $this->parameters = $this->matcher->match($this->context->getPathInfo());
    }

    /**
     * @return FileLocator
     */
    public function getFileLocator(): FileLocator
    {
        return $this->fileLocator;
    }

    /**
     * @return YamlFileLoader
     */
    public function getLoader(): YamlFileLoader
    {
        return $this->loader;
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * @return RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * @return UrlMatcher
     */
    public function getMatcher(): UrlMatcher
    {
        return $this->matcher;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
