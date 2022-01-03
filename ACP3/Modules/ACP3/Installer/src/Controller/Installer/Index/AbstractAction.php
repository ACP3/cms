<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation;

abstract class AbstractAction extends AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        protected Navigation $navigation)
    {
        parent::__construct($context);
    }

    public function preDispatch(): void
    {
        parent::preDispatch();

        $this->navigation->setProgress($this->request);

        $this->view->assign('navbar', $this->navigation->all());
    }
}
