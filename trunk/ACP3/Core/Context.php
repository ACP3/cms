<?php
namespace ACP3\Core;

/**
 * Class Context
 * @package ACP3\Core
 */
class Context
{
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
     * @var URI
     */
    protected $uri;
    /**
     * @var View
     */
    protected $view;

    public function __construct(
        Auth $auth,
        Lang $lang,
        Modules $modules,
        URI $uri,
        View $view)
    {
        $this->auth = $auth;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->uri = $uri;
        $this->view = $view;
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
     * @return \ACP3\Core\URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

} 