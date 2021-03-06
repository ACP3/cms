<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Breadcrumb\Title;

class SiteSubtitle extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;

    public function __construct(Title $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->title->getSiteSubtitle();
    }
}
