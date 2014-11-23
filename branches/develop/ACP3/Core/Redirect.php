<?php
namespace ACP3\Core;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Redirect
 * @package ACP3\Core
 */
class Redirect
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Router
     */
    protected $router;

    /**
     * @param Request $request
     * @param Router $router
     */
    public function __construct(
        Request $request,
        Router $router
    )
    {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @param $url
     */
    public function toNewPage($url)
    {
        if ($this->request->getIsAjax() === true) {
            $this->_ajax($url);
        }

        $response = new RedirectResponse($url);
        $response->send();
    }

    /**
     * Executes a temporary redirect
     *
     * @param $path
     */
    public function temporary($path)
    {
        $this->_redirect($path, false);
    }

    /**
     * Umleitung auf andere URLs
     *
     * @param string $path
     * @param bool   $movedPermanently
     */
    private function _redirect($path, $movedPermanently)
    {
        $path = $this->router->getProtocol() . $this->router->getHostname() . $this->router->route($path);

        if ($this->request->getIsAjax() === true) {
            $this->_ajax($path);
        }

        $status = 302;
        if ($movedPermanently === true) {
            $status = 301;
        }

        $response = new RedirectResponse($path, $status);
        $response->send();
        exit;
    }

    /**
     * Outputs a JSON response with redirect url
     *
     * @param $path
     */
    private function _ajax($path)
    {
        if ($this->request->getIsAjax() === true) {
            $return = array(
                'redirect_url' => $path
            );

            $response = new JsonResponse($return);
            $response->send();
            exit;
        }
    }

    /**
     * Executes a permanent redirect
     *
     * @param $path
     */
    public function permanent($path)
    {
        $this->_redirect($path, true);
    }

} 