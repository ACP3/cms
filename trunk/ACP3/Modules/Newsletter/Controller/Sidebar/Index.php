<?php

namespace ACP3\Modules\Newsletter\Controller\Sidebar;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\Newsletter\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * @param Core\Context $context
     * @param Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Core\Context $context,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
    }

    /**
     * @param string $template
     */
    public function actionIndex($template = '')
    {
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->secureHelper->generateFormToken('newsletter/index/index');

        $this->setTemplate($template !== '' ? $template : 'Newsletter/Sidebar/index.index.tpl');
    }
}
