<?php

namespace ACP3\Modules\Newsletter\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Description of NewsletterFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{

    public function actionIndex()
    {
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->session->generateFormToken('newsletter/index/index');

        $this->setLayout('Newsletter/Sidebar/index.index.tpl');
    }

}