<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RedirectResponse
 * @package ACP3\Core\Http
 */
class RedirectResponse
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;

    /**
     * Redirect constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\RouterInterface       $router
     */
    public function __construct(
        RequestInterface $request,
        RouterInterface $router
    ) {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @param string $url
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toNewPage($url)
    {
        if ($this->request->isAjax() === true) {
            return $this->ajaxRedirect($url);
        }

        return new SymfonyRedirectResponse($url);
    }

    /**
     * Executes a temporary redirect
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function temporary($path)
    {
        return $this->redirect($path, Response::HTTP_FOUND);
    }

    /**
     * Redirect to an other URLs
     *
     * @param string $path
     * @param int    $statusCode
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirect($path, $statusCode)
    {
        $path = $this->router->route($path, true);

        if ($this->request->isAjax() === true) {
            return $this->ajaxRedirect($path);
        }

        return new SymfonyRedirectResponse($path, $statusCode);
    }

    /**
     * Outputs a JSON response with a redirect url
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function ajaxRedirect($path)
    {
        $return = [];
        if ($this->request->isAjax() === true) {
            $return['redirect_url'] = $path;
        }

        return new JsonResponse($return);
    }

    /**
     * Executes a permanent redirect
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function permanent($path)
    {
        return $this->redirect($path, Response::HTTP_MOVED_PERMANENTLY);
    }
}
