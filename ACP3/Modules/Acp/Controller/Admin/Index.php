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
        $modules = $this->modules->getActiveModules();
        $mods = array();

        foreach ($modules as $name => $info) {
            $dir = strtolower($info['dir']);
            if ($this->modules->hasPermission('admin/' . $dir) === true && $dir !== 'acp') {
                $mods[$name]['name'] = $name;
                $mods[$name]['dir'] = $dir;
            }
        }
        $this->view->assign('modules', $mods);
    }

}
