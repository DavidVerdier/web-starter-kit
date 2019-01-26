<?php
namespace App\Kernel\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    public function getParameter(string $key);

    public function render(string $view, array $parameters = array(), Response $response = null): Response;

    public function redirect(string $url, int $status = 302): RedirectResponse;

    public function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse;

    public function generateUrl(string $routeName, ?array $params = null): string;

    public function getDoctrine(): EntityManager;
}
