<?php
namespace ACP3\Core\Modules\Controller;

use ACP3\Core;
use ACP3\Core\Modules\Controller\Context;

/**
 * Class FrontendContext
 * @package ACP3\Core\Modules\Controller
 */
class FrontendContext extends Core\Modules\Controller\Context
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;

    /**
     * @param \ACP3\Core\Modules\Controller\Context $context
     * @param \ACP3\Core\Assets                     $assets
     * @param \ACP3\Core\Breadcrumb                 $breadcrumb
     * @param \ACP3\Core\SEO                        $seo
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Assets $assets,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo
    )
    {
        parent::__construct(
            $context->getACL(),
            $context->getAuth(),
            $context->getLang(),
            $context->getModules(),
            $context->getRequest(),
            $context->getRouter(),
            $context->getView(),
            $context->getConfig()
        );

        $this->assets = $assets;
        $this->breadcrumb = $breadcrumb;
        $this->seo = $seo;
    }

    /**
     * @return Core\Assets
     */
    public function getAssets()
    {
        return $this->assets;
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
