<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

class PageTitle extends SiteTitle
{
    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->title->getPageTitle();
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'page_title';
    }
}
