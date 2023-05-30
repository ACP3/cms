<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

class PageTitle extends SiteTitle
{
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        return $this->title->getPageTitle();
    }
}
