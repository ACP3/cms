<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Sidebar\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Newsletter\Controller\CaptchaHelperTrait;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    use CaptchaHelperTrait;

    /**
     * @var Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\Context $context
     * @param \ACP3\Core\Helpers\FormToken          $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param string $template
     */
    public function execute($template = '')
    {
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->formTokenHelper->generateFormToken('newsletter/index/index');

        $this->setTemplate($template !== '' ? $template : 'Newsletter/Sidebar/index.index.tpl');
    }
}
