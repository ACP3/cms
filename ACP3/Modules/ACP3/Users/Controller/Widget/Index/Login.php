<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;

/**
 * Class Login
 * @package ACP3\Modules\ACP3\Users\Controller\Widget\Index
 */
class Login extends Core\Controller\WidgetController
{
    /**
     * Displays the login mask, if the user is not already logged in
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === false) {
            $currentPage = base64_encode(($this->request->getArea() === 'admin' ? 'acp/' : '') . $this->request->getQuery());

            $settings = $this->config->getSettings('users');

            $this->view->assign('enable_registration', $settings['enable_registration']);
            $this->view->assign('redirect_uri', $this->request->getPost()->get('redirect_uri', $currentPage));

            $this->setTemplate('Users/Widget/index.login.tpl');
        } else {
            $this->setNoOutput(true);
        }
    }
}
