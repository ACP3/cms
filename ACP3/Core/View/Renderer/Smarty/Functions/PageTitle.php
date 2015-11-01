<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

/**
 * Class PageTitle
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class PageTitle extends SiteTitle
{
    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->breadcrumb->getPageTitle();
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'page_title';
    }

}