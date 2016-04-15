<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Captcha\Helpers;

/**
 * Class OnFormAfterListener
 * @package ACP3\Modules\ACP3\Captcha\Event\Listener
 */
class OnDisplayCaptchaListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelper;

    /**
     * OnAfterFormListener constructor.
     *
     * @param \ACP3\Core\ACL                     $acl
     * @param \ACP3\Modules\ACP3\Captcha\Helpers $captchaHelper
     */
    public function __construct(ACL $acl, Helpers $captchaHelper)
    {
        $this->acl = $acl;
        $this->captchaHelper = $captchaHelper;
    }

    /**
     * @param \ACP3\Core\View\Event\TemplateEvent $templateEvent
     */
    public function onDisplayCaptcha(TemplateEvent $templateEvent)
    {
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $arguments = $templateEvent->getParameters();
            echo $this->captchaHelper->captcha(
                isset($arguments['length']) ? $arguments['length'] : Helpers::CAPTCHA_DEFAULT_LENGTH,
                isset($arguments['input_id']) ? $arguments['input_id'] : Helpers::CAPTCHA_DEFAULT_INPUT_ID,
                isset($arguments['input_only']) ? $arguments['input_only'] : false,
                isset($arguments['path']) ? $arguments['path'] : ''
            );
        }
    }
}
