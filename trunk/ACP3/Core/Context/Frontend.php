<?php
namespace ACP3\Core\Context;

use ACP3\Core;

/**
 * Class Frontend
 * @package ACP3\Core\Context
 */
class Frontend extends Core\Context
{
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;

    public function __construct(
        Core\Auth $auth,
        Core\Lang $lang,
        Core\Modules $modules,
        Core\URI $uri,
        Core\View $view,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo)
    {
        parent::__construct($auth, $lang, $modules, $uri, $view);

        $this->breadcrumb = $breadcrumb;
        $this->seo = $seo;
    }

    /**
     * @return \ACP3\Core\Breadcrumb
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @return \ACP3\Core\SEO
     */
    public function getSeo()
    {
        return $this->seo;
    }

} 