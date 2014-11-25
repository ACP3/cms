<?php

namespace ACP3\Modules\Acp\Controller\Admin;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\Acp\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
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
        $this->view->assign('modules', $allowedModules);
    }
}
