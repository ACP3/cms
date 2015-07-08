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
     * @param \ACP3\Core\Router           $router
     */
    public function __construct(
        RequestInterface $request,
        Router $router
    ) {
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
     * Redirect to an other URLs
     *
     * @param string $path
     * @param bool   $movedPermanently
     */
    private function _redirect($path, $movedPermanently)
    {
        $path = $this->router->route($path, true);

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
            $return = [
                'redirect_url' => $path
            ];

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
