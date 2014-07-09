<?php

namespace ACP3\Modules\Newsletter\Controller\Sidebar;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\Newsletter\Controller\Sidebar
 */
class Index extends Core\Modules\Controller\Sidebar
{

    public function actionIndex()
    {
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->session->generateFormToken('newsletter/index/index');

        $this->setLayout('Newsletter/Sidebar/index.index.tpl');
    }

}