<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Breadcrumb\Title;

/**
 * Class SiteTitle
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class SiteTitle extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;

    /**
     * @param \ACP3\Core\Breadcrumb\Title $title
     */
    public function __construct(Title $title)
    {
        $this->title = $title;
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->title->getSiteTitle();
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'site_title';
    }
}
