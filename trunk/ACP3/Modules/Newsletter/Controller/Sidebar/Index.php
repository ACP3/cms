<?php

namespace ACP3\Modules\Newsletter\Controller\Sidebar;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\Newsletter\Controller\Sidebar
 */
class Index extends Core\Modules\Controller\Sidebar
{
    /**
     * @var Core\Session
     */
    protected $session;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Session $session)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->session = $session;
    }


    public function actionIndex()
    {
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->session->generateFormToken('newsletter/index/index');

        $this->setLayout('Newsletter/Sidebar/index.index.tpl');
    }

}