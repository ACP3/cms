<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Breadcrumb\Steps;

class Breadcrumb extends AbstractFunction
{
    /**
     * @var Steps
     */
    private $breadcrumb;

    /**
     * Breadcrumb constructor.
     * @param Steps $breadcrumb
     */
    public function __construct(Steps $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $smarty->assign('breadcrumb', $this->breadcrumb->getBreadcrumb());

        return $smarty->fetch('asset:System/Partials/breadcrumb.tpl');
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'breadcrumb';
    }
}
