<?php
namespace ACP3\Core;

/**
 * Class Context
 * @package ACP3\Core
 */
class Context
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\ACL     $acl
     * @param \ACP3\Core\Auth    $auth
     * @param \ACP3\Core\Lang    $lang
     * @param \ACP3\Core\Modules $modules
     * @param \ACP3\Core\Request $request
     * @param \ACP3\Core\Router  $router
     * @param \ACP3\Core\View    $view
     * @param \ACP3\Core\Config  $config
     */
    public function __construct(
        ACL $acl,
        Auth $auth,
        Lang $lang,
        Modules $modules,
        Request $request,
        Router $router,
        View $view,
        Config $config
    ) {
        $this->acl = $acl;
        $this->auth = $auth;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->config = $config;
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

    /**
     * @return \ACP3\Core\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
