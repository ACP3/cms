<?php
namespace ACP3\Core;

use ACP3\Core\Http\RequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Router                $router
     */
    public function __construct(
        RequestInterface $request,
        Router $router
    )
    {
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
            return $this->_ajax($url);
        }

        return new RedirectResponse($url);
    }

    /**
     * Executes a temporary redirect
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function temporary($path)
    {
        return $this->_redirect($path, false);
    }

    /**
     * Redirect to an other URLs
     *
     * @param string $path
     * @param bool   $movedPermanently
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _redirect($path, $movedPermanently)
    {
        $path = $this->router->route($path, true);

        if ($this->request->isAjax() === true) {
            return $this->_ajax($path);
        }

        $status = 302;
        if ($movedPermanently === true) {
            $status = 301;
        }

        return new RedirectResponse($path, $status);
    }

    /**
     * Outputs a JSON response with redirect url
     *
     * @param $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function _ajax($path)
    {
        if ($this->request->isAjax() === true) {
            $return = [
                'redirect_url' => $path
            ];

            return new JsonResponse($return);
        }

        return new JsonResponse();
    }

    /**
     * Executes a permanent redirect
     *
     * @param $path
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function permanent($path)
    {
        return $this->_redirect($path, true);
    }
}
