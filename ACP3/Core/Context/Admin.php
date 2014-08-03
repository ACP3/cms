<?php
namespace ACP3\Core\Context;

use ACP3\Core;

/**
 * Class Admin
 * @package ACP3\Core\Context
 */
class Admin extends Frontend
{
    /**
     * @var \ACP3\Core\Validate
     */
    protected $validate;
    /**
     * @var \ACP3\Core\Session
     */
    protected $session;
    /**
     * @var Core\Router\Aliases
     */
    protected $aliases;

    public function __construct(
        Core\Auth $auth,
        Core\Lang $lang,
        Core\Modules $modules,
        Core\Request $request,
        Core\Router $router,
        Core\View $view,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session,
        Core\Router\Aliases $aliases)
    {
        parent::__construct($auth, $lang, $modules, $request, $router, $view, $breadcrumb, $seo);

        $this->validate = $validate;
        $this->session = $session;
        $this->aliases = $aliases;
    }

    /**
     * @return \ACP3\Core\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \ACP3\Core\Validate
     */
    public function getValidate()
    {
        return $this->validate;
    }

    public function getAliases()
    {
        return $this->aliases;
    }

} 