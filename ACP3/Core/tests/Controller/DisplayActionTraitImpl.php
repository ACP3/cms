<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\View;

class DisplayActionTraitImpl
{
    use DisplayActionTrait;

    public function __construct(private View $view)
    {
    }

    protected function applyTemplateAutomatically(): string
    {
        return 'Foo/Frontend/index.index.tpl';
    }

    protected function getView(): View
    {
        return $this->view;
    }
}
