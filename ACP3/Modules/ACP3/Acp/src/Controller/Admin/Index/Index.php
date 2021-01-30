<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Acp\ViewProviders\ModuleListViewProvider;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Acp\ViewProviders\ModuleListViewProvider
     */
    private $modulesListViewProvider;

    public function __construct(WidgetContext $context, ModuleListViewProvider $modulesListViewProvider)
    {
        parent::__construct($context);

        $this->modulesListViewProvider = $modulesListViewProvider;
    }

    public function execute(): array
    {
        return ($this->modulesListViewProvider)();
    }
}
