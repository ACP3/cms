<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaFactory;

class OnDisplayCaptchaListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var CaptchaFactory
     */
    private $captchaFactory;

    /**
     * OnAfterFormListener constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param CaptchaFactory $captchaFactory
     */
    public function __construct(ACL $acl, CaptchaFactory $captchaFactory)
    {
        $this->acl = $acl;
        $this->captchaFactory = $captchaFactory;
    }

    /**
     * @param \ACP3\Core\View\Event\TemplateEvent $templateEvent
     */
    public function onDisplayCaptcha(TemplateEvent $templateEvent)
    {
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $arguments = $templateEvent->getParameters();

            $captcha = $this->captchaFactory->create();
            echo $captcha->getCaptcha(
                isset($arguments['length']) ? $arguments['length'] : CaptchaExtensionInterface::CAPTCHA_DEFAULT_LENGTH,
                isset($arguments['input_id']) ? $arguments['input_id'] : CaptchaExtensionInterface::CAPTCHA_DEFAULT_INPUT_ID,
                isset($arguments['input_only']) ? $arguments['input_only'] : false,
                isset($arguments['path']) ? $arguments['path'] : ''
            );
        }
    }
}
