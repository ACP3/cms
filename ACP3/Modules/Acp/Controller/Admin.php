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

    public function __construct(
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);
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
