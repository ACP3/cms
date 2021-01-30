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

    /**
     * @var View
     */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    protected function applyTemplateAutomatically(): string
    {
        return 'Foo/Frontend/index.index.tpl';
    }

    /**
     * @return \ACP3\Core\View
     */
    protected function getView()
    {
        return $this->view;
    }
}
