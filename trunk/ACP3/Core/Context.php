<?php
namespace ACP3\Core;

/**
 * Class Context
 * @package ACP3\Core
 */
class Context
{
    /**
     * @var ACL
     */
    protected $acl;
    /**
     * @var Auth
     */
    protected $auth;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var Modules
     */
    protected $modules;
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

    /**
     * @param ACL $acl
     * @param Auth $auth
     * @param Lang $lang
     * @param Modules $modules
     * @param Request $request
     * @param Router $router
     * @param View $view
     */
    public function __construct(
        ACL $acl,
        Auth $auth,
        Lang $lang,
        Modules $modules,
        Request $request,
        Router $router,
        View $view)
    {
        $this->acl = $acl;
        $this->auth = $auth;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
    }

    /**
     * @return \ACP3\Core\ACL
     */
    public function getACL()
    {
        return $this->acl;
    }

    /**
     * @return \ACP3\Core\Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @return \ACP3\Core\Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return \ACP3\Core\Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @return \ACP3\Core\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \ACP3\Core\Router
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