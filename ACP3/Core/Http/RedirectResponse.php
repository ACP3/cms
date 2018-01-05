<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Router\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectResponse
{
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;

    /**
     * Redirect constructor.
     *
     * @param \ACP3\Core\Router\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $url
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toNewPage(string $url)
    {
        return new SymfonyRedirectResponse($url);
    }

    /**
     * Executes a temporary redirect
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function temporary(string $path)
    {
        return $this->createRedirectResponse($path, Response::HTTP_FOUND);
    }

    /**
     * Redirect to an other URLs
     *
     * @param string $path
     * @param int $statusCode
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function createRedirectResponse(string $path, int $statusCode)
    {
        return new SymfonyRedirectResponse($this->router->route($path, true), $statusCode);
    }

    /**
     * Executes a permanent redirect
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function permanent(string $path)
    {
        return $this->createRedirectResponse($path, Response::HTTP_MOVED_PERMANENTLY);
    }
}
