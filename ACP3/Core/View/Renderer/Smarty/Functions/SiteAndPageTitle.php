<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

/**
 * Class SiteAndPageTitle
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class SiteAndPageTitle extends SiteTitle
{
    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->title->getSiteAndPageTitle();
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'site_and_page_title';
    }
}
