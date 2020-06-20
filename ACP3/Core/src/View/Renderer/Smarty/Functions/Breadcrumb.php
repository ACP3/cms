<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Breadcrumb\Steps;

class Breadcrumb extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;

    public function __construct(Steps $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $smarty->smarty->assign('breadcrumb', $this->breadcrumb->getBreadcrumb());

        return $smarty->smarty->fetch('asset:System/Partials/breadcrumb.tpl');
    }

    public function getExtensionName()
    {
        return 'breadcrumb';
    }
}
