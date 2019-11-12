<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Controller\Admin\Index;

use ACP3\Core;

class Index extends Core\Controller\AbstractFrontendAction
{
    public function execute(): array
    {
        return [
            'modules' => $this->getAllowedModules(),
        ];
    }

    protected function getAllowedModules(): array
    {
        $allowedModules = [];

        foreach ($this->modules->getActiveModules() as $info) {
            $moduleName = \strtolower($info['name']);
            if ($moduleName !== 'acp' && $this->acl->hasPermission('admin/' . $moduleName) === true) {
                $allowedModules[$moduleName] = [
                    'name' => $moduleName,
                ];
            }
        }

        return $allowedModules;
    }
}
