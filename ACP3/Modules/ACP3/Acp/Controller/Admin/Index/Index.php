<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Controller\Admin\Index;

use ACP3\Core;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @return array
     */
    public function execute()
    {
        return [
            'modules' => $this->getAllowedModules(),
        ];
    }

    /**
     * @return array
     */
    protected function getAllowedModules()
    {
        $allowedModules = [];

        foreach ($this->modules->getActiveModules() as $name => $info) {
            $dir = \strtolower($info['dir']);
            if ($this->acl->hasPermission('admin/' . $dir) === true && $dir !== 'acp') {
                $allowedModules[$name] = [
                    'name' => $name,
                    'dir' => $dir,
                ];
            }
        }

        return $allowedModules;
    }
}
