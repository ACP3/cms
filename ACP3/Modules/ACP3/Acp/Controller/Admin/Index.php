<?php

namespace ACP3\Modules\ACP3\Acp\Controller\Admin;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Acp\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @return array
     */
    public function actionIndex()
    {
        $activeModules = $this->modules->getActiveModules();
        $allowedModules = [];

        foreach ($activeModules as $name => $info) {
            $dir = strtolower($info['dir']);
            if ($this->acl->hasPermission('admin/' . $dir) === true && $dir !== 'acp') {
                $allowedModules[$name]['name'] = $name;
                $allowedModules[$name]['dir'] = $dir;
            }
        }

        return [
            'modules' => $allowedModules
        ];
    }
}
