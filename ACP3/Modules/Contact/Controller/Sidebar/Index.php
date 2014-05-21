<?php

namespace ACP3\Modules\Contact\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Sidebar controller of the contacts module
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{
    public function actionIndex()
    {
        $settings = Core\Config::getSettings('contact');
        $settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
        $settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
        $this->view->assign('sidebar_contact', $settings);

        $this->setLayout('Contact/Sidebar/index.index.tpl');
    }

}