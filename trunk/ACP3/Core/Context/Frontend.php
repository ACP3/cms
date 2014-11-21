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
     * @var Core\Assets
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
     * @param Core\Context $context
     * @param Core\Assets $assets
     * @param Core\Breadcrumb $breadcrumb
     * @param Core\SEO $seo
     */
    public function __construct(
        Core\Context $context,
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
            $context->getSystemConfig()
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