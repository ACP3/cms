<?php
namespace ACP3\Installer\Core;

/**
 * Class Context
 * @package ACP3\Core
 */
class Context
{
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param Lang $lang
     * @param Request $request
     * @param Router $router
     * @param \ACP3\Core\View $view
     */
    public function __construct(
        Lang $lang,
        Request $request,
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
