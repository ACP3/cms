<?php

namespace ACP3\Modules\Acp\Controller\Admin;

use ACP3\Core;

/**
 * Module Controller of the Admin Backend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{

    public function actionIndex()
    {
        $mod_list = Core\Modules::getAllModules();
        $mods = array();

        foreach ($mod_list as $name => $info) {
            $dir = strtolower($info['dir']);
            if (Core\Modules::hasPermission('admin/' . $dir) === true && $dir !== 'acp') {
                $mods[$name]['name'] = $name;
                $mods[$name]['dir'] = $dir;
            }
        }
        $this->view->assign('modules', $mods);
    }

}
