<?php

namespace App\Kernel;

use App\Kernel\Exception\HttpException;
use App\Kernel\Routing\Router;
use App\Twig\Extension\AssetExtension;
use App\Twig\Extension\DumpExtension;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Container
{
    private $asset;

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

            $this->twig->addExtension(new AssetExtension($this->getAsset()));
            $this->twig->addExtension(new DumpExtension());
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
     * @return Package
     */
    public function getAsset() : Package
    {
        if (null === $this->asset) {
            $manifest = $this->getConfiguration()->getParameter('assets_json_manifest_path');

            if (null === $manifest) {
                $version = new EmptyVersionStrategy();
            } else {
                $version = new JsonManifestVersionStrategy($manifest);
            }

            $this->asset =  new Package($version);
        }

        return $this->asset;
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
