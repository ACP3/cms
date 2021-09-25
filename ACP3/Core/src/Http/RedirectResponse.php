<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Router\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectResponse
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;

    public function __construct(
        RequestInterface $request,
        RouterInterface $router
    ) {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toNewPage(string $url)
    {
        if ($this->request->isXmlHttpRequest() === true) {
            return $this->createAjaxRedirectResponse($url);
        }

        return new SymfonyRedirectResponse($url);
    }

    /**
     * Executes a temporary redirect.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function temporary(string $path)
    {
        return $this->createRedirectResponse($path, Response::HTTP_FOUND);
    }

    /**
     * Redirect to an other URLs.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function createRedirectResponse(string $path, int $statusCode)
    {
        $path = $this->router->route($path, true);

        if ($this->request->isXmlHttpRequest() === true) {
            return $this->createAjaxRedirectResponse($path);
        }

        return new SymfonyRedirectResponse($path, $statusCode);
    }

    /**
     * Outputs a JSON response with a redirect url.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function createAjaxRedirectResponse(string $path)
    {
        return new JsonResponse(['redirect_url' => $path]);
    }

    /**
     * Executes a permanent redirect.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function permanent(string $path)
    {
        return $this->createRedirectResponse($path, Response::HTTP_MOVED_PERMANENTLY);
    }
}
