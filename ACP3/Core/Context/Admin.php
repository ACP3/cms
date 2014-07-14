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

    public function __construct(
        Core\Auth $auth,
        Core\Lang $lang,
        Core\Modules $modules,
        Core\URI $uri,
        Core\View $view,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session)
    {
        parent::__construct($auth, $lang, $modules, $uri, $view, $breadcrumb, $seo);

        $this->validate = $validate;
        $this->session = $session;
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

} 