<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Modules\ACP3\Installer\Core\Controller\AbstractInstallerAction;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation;

abstract class AbstractAction extends AbstractInstallerAction
{
    /**
     * @var Navigation
     */
    protected $navigation;

    public function __construct(
        InstallerContext $context,
        Navigation $navigation)
    {
        parent::__construct($context);

        $this->navigation = $navigation;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->navigation->setProgress($this->request);

        $this->view->assign('navbar', $this->navigation->all());
    }
}
