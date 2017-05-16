<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core\Controller\AbstractFrontendAction;

class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @param string $moduleName
     * @return int
     */
    protected function fetchModuleId(string $moduleName): int
    {
        $moduleInfo = $this->modules->getModuleInfo($moduleName);

        return isset($moduleInfo['id']) ? (int)$moduleInfo['id'] : 0;
    }
}
