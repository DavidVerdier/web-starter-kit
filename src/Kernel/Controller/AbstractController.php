<?php

namespace App\Kernel\Controller;

use App\Kernel\Container;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AbstractController implements ControllerInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * AbstractController constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $key
     * @return null
     */
    public function getParameter(string $key)
    {
        return $this->container->getConfiguration()->getParameter($key);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $content = $this->container->getTwig()->render($view, $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response->send();
    }

    /**
     * @param string $url
     * @param int $status
     * @return RedirectResponse
     */
    public function redirect(string $url, int $status = 302): RedirectResponse
    {
        $response = new RedirectResponse($url, $status);

        return $response->send();
    }

    /**
     * @param string $route
     * @param array $parameters
     * @param int $status
     * @return RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * @param string $routeName
     * @param array|null $params
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generateUrl(string $routeName, ?array $params = null): string
    {
        return $this->container->getRouter()->generate($routeName, $params = array());
    }

    /**
     * @return EntityManager
     */
    public function getDoctrine(): EntityManager
    {
        return $this->container->getDoctrine();
    }
}
