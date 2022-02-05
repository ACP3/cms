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
    public function __construct(private RequestInterface $request, private RouterInterface $router)
    {
    }

    public function toNewPage(string $url): JsonResponse|SymfonyRedirectResponse
    {
        if ($this->request->isXmlHttpRequest() === true) {
            return $this->createAjaxRedirectResponse($url);
        }

        return new SymfonyRedirectResponse($url);
    }

    /**
     * Executes a temporary redirect.
     */
    public function temporary(string $path): JsonResponse|SymfonyRedirectResponse
    {
        return $this->createRedirectResponse($path, Response::HTTP_FOUND);
    }

    /**
     * Redirect to another URLs.
     */
    private function createRedirectResponse(string $path, int $statusCode): JsonResponse|SymfonyRedirectResponse
    {
        $path = $this->router->route($path, true);

        if ($this->request->isXmlHttpRequest() === true) {
            return $this->createAjaxRedirectResponse($path);
        }

        return new SymfonyRedirectResponse($path, $statusCode);
    }

    /**
     * Outputs a JSON response with a redirect url.
     */
    private function createAjaxRedirectResponse(string $path): JsonResponse
    {
        return new JsonResponse(['redirect_url' => $path]);
    }

    /**
     * Executes a permanent redirect.
     */
    public function permanent(string $path): JsonResponse|SymfonyRedirectResponse
    {
        return $this->createRedirectResponse($path, Response::HTTP_MOVED_PERMANENTLY);
    }
}
