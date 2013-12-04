<?php

namespace ACP3\Modules\Acp\Controller;

use ACP3\Core;

/**
 * Module Controller of the Admin Backend
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function actionList()
    {
        $mod_list = Core\Modules::getAllModules();
        $mods = array();

        foreach ($mod_list as $name => $info) {
            $dir = strtolower($info['dir']);
            if (Core\Modules::hasPermission($dir, 'acp_list') === true && $dir !== 'acp') {
                $mods[$name]['name'] = $name;
                $mods[$name]['dir'] = $dir;
            }
        }
        $this->view->assign('modules', $mods);
    }

}
