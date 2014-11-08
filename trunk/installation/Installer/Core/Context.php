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
     * @var View
     */
    protected $view;

    public function __construct(
        Lang $lang,
        Request $request,
        Router $router,
        View $view)
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
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

}