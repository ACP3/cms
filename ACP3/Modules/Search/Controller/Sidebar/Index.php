<?php

namespace ACP3\Modules\Search\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Search;

/**
 * Description of SearchFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{
    public function actionIndex()
    {
        $this->view->assign('search_mods', $this->get('search.helpers')->getModules());

        $this->session->generateFormToken('search/index/index');

        $this->setLayout('Search/Sidebar/index.index.tpl');
    }

}