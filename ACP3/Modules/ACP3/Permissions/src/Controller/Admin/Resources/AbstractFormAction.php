<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Modules;

abstract class AbstractFormAction extends AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        WidgetContext $context,
        Modules $modules
    ) {
        parent::__construct($context);

        $this->modules = $modules;
    }

    protected function fetchModuleId(string $moduleName): int
    {
        $moduleInfo = $this->modules->getModuleInfo($moduleName);

        return $moduleInfo['id'] ?? 0;
    }
}
