<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\Context           $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Captcha\Helpers $captchaHelpers
     *
     * @return $this
     */
    public function setCaptchaHelpers(Captcha\Helpers $captchaHelpers)
    {
        $this->captchaHelpers = $captchaHelpers;

        return $this;
    }

    /**
     * @param string $template
     */
    public function actionIndex($template = '')
    {
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->formTokenHelper->generateFormToken('newsletter/index/index');

        $this->setTemplate($template !== '' ? $template : 'Newsletter/Sidebar/index.index.tpl');
    }
}
