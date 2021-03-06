<?php

namespace App\Kernel;

use App\Kernel\Bridges\Routing;
use App\Kernel\Exception\HttpException;
use App\Twig\Extension\AssetExtension;
use App\Twig\Extension\DumpExtension;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Container
{
    /**
     * @var Package
     */
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
     * @var Routing
     */
    private $router;

    /**
     * @var EntityManager
     */
    private $doctrine;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Session
     */
    private $session;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();

        $this->session = new Session();
        $this->session->start();
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function start()
    {
        $this->getRouter();
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig(): \Twig_Environment
    {
        if (null === $this->twig) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/../templates');

            $this->twig = new \Twig_Environment($loader, [
                'cache' => false
            ]);

            $this->twig->addExtension(new AssetExtension($this->getAsset()));
            $this->twig->addExtension(new DumpExtension());

            $this->twig->addGlobal('client_name', 'Container add global');
        }

        return $this->twig;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        if (null === $this->config) {
            $this->config = new Configuration();
        }

        return $this->config;
    }

    /**
     * @return Package
     */
    public function getAsset(): Package
    {
        if (null === $this->asset) {
            $manifest = $this->getConfiguration()->getParameter('assets_json_manifest_path');

            if (null === $manifest) {
                $version = new EmptyVersionStrategy();
            } else {
                $version = new JsonManifestVersionStrategy($manifest);
            }

            $this->asset = new Package($version);
        }

        return $this->asset;
    }

    /**
     * @return Routing
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getRouter(): Routing
    {
        if (null === $this->router) {
            try {
                $this->router = new Routing($this);
                $params = $this->router->getParameters();
                $data = explode('::', $params["_controller"]);
                $controller = $data[0];
                $method = $data[1];
                $realParams = array();

                foreach ($params as $k => $v) {
                    if ("_" !== substr( $k, 0, 1 )) {
                        $realParams[$k] = $v;
                    }
                }

                $object =  new $controller($this);

                return call_user_func_array(array($object, $method), $realParams);
            } catch (\Exception $e) {
                if ($e instanceof HttpException || $e instanceof ResourceNotFoundException) {
                    $response = new Response(
                        $e->getMessage(),
                        Response::HTTP_NOT_FOUND,
                        ['content-type' => 'text/html']
                    );

                    $response->setContent(
                        $this->getTwig()->render('_errors/error.html.twig', ['name' => 'Fabien'])
                    );
                    $response->send();
                }
            }
        }

        return $this->router;
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public function getDoctrine(): EntityManager
    {
        if (null === $this->doctrine) {
            $isDevMode = $this->getConfiguration()->getParameter('env') === 'prod' ? false : true;
            $proxyDir = null;
            $cache = null;
            $useSimpleAnnotationReader = false;

            $config = Setup::createAnnotationMetadataConfiguration(
                [__DIR__ . "/../../src/Entity"],
                $isDevMode,
                $proxyDir,
                $cache,
                $useSimpleAnnotationReader
            );

            $conn = array(
                'driver' => $this->getConfiguration()->getParameter('database_driver'),
                'user' => $this->getConfiguration()->getParameter('database_user'),
                'password' => $this->getConfiguration()->getParameter('database_password'),
                'dbname' => $this->getConfiguration()->getParameter('database_name'),
                'host' => $this->getConfiguration()->getParameter('database_host'),
                'port' => $this->getConfiguration()->getParameter('database_port'),
            );

            $this->doctrine = EntityManager::create($conn, $config);
        }

        return $this->doctrine;
    }

    /**
     * @return \Symfony\Component\Console\Helper\HelperSet
     * @throws \Doctrine\ORM\ORMException
     */
    public function doctrineConsoleRunner()
    {
        return ConsoleRunner::createHelperSet($this->getDoctrine());
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }
}
