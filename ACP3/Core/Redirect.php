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
     * @var string
     */
    protected $protocol = '';
    /**
     * @var string
     */
    protected $hostname = '';

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Router
     */
    protected $router;

    public function __construct(Request $request, Router $router)
    {
        $this->request = $request;
        $this->router = $router;

        $this->protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) === 'off' ? 'http://' : 'https://';
        $this->hostname = $_SERVER['HTTP_HOST'];
    }

    /**
     * @param $url
     */
    public function toNewPage($url)
    {
        $response = new RedirectResponse($url);
        $response->send();
    }

    /**
     * Outputs a JSON response with redirect url
     *
     * @param $path
     */
    public function ajax($path)
    {
        $url = $this->protocol . $this->hostname . $this->router->route($path);

        if ($this->request->getIsAjax() === true) {
            $return = array(
                'redirect_url' => $url
            );

            $response = new JsonResponse($return);
            $response->send();
            exit;
        }
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
        if ($this->request->getIsAjax() === true) {
            $this->ajax($path);
        }

        $url = $this->protocol . $this->hostname . $this->router->route($path);

        $status = 302;
        if ($movedPermanently === true) {
            $status = 301;
        }

        $response = new RedirectResponse($url, $status);
        $response->send();
        exit;
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