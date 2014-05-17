<?php

namespace ACP3\Modules\Search\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Search;

/**
 * Description of SearchFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    public function actionIndex()
    {
        $this->view->assign('search_mods', Search\Helpers::getModules());

        $this->session->generateFormToken('search/index/index');

        $this->view->displayTemplate('search/sidebar.tpl');
    }

}