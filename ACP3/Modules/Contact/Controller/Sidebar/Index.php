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
        $formatter = $this->get('core.helpers.string.formatter');

        $config = new Core\Config($this->db, 'contact');
        $settings = $config->getSettings();
        $settings['address'] = $formatter->rewriteInternalUri($settings['address']);
        $settings['disclaimer'] = $formatter->rewriteInternalUri($settings['disclaimer']);
        $this->view->assign('sidebar_contact', $settings);

        $this->setLayout('Contact/Sidebar/index.index.tpl');
    }

}