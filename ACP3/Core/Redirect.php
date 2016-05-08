<?php
namespace ACP3\Core;

use ACP3\Core\Http\RequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Redirect
 * @package ACP3\Core
 */
class Redirect
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

        return new RedirectResponse($url);
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
        return $this->redirect($path, false);
    }

    /**
     * Redirect to an other URLs
     *
     * @param string $path
     * @param bool   $movedPermanently
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirect($path, $movedPermanently)
    {
        $path = $this->router->route($path, true);

        if ($this->request->isAjax() === true) {
            return $this->ajaxRedirect($path);
        }

        $status = Response::HTTP_FOUND;
        if ($movedPermanently === true) {
            $status = Response::HTTP_MOVED_PERMANENTLY;
        }

        return new RedirectResponse($path, $status);
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
        return $this->redirect($path, true);
    }
}
