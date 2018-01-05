<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

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
