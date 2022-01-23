<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Breadcrumb\Title;

class SiteSubtitle extends AbstractFunction
{
    public function __construct(protected Title $title)
    {
    }

    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        return $this->title->getSiteSubtitle();
    }
}
