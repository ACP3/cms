<?php
namespace ACP3\Installer\Core\Modules\Controller;

use ACP3\Core\Http\RequestInterface;
use ACP3\Installer\Core\Http\Request;
use ACP3\Installer\Core\Lang;
use ACP3\Installer\Core\Router;

/**
 * Class Context
 * @package ACP3\Installer\Core\Modules\Controller
 */
class Context
{
    /**
     * @var \ACP3\Installer\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param \ACP3\Installer\Core\Lang        $lang
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Installer\Core\Router      $router
     * @param \ACP3\Core\View                  $view
     */
    public function __construct(
        Lang $lang,
        RequestInterface $request,
        Router $router,
        \ACP3\Core\View $view)
    {
        $this->lang = $lang;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
    }

    /**
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }
}
