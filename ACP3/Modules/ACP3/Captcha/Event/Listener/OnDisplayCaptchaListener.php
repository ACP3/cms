<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;

class OnDisplayCaptchaListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var CaptchaExtensionInterface
     */
    private $captchaExtension;

    /**
     * OnDisplayCaptchaListener constructor.
     *
     * @param ACL $acl
     * @param CaptchaExtensionInterface $captchaExtension
     */
    public function __construct(ACL $acl, CaptchaExtensionInterface $captchaExtension = null)
    {
        $this->acl = $acl;
        $this->captchaExtension = $captchaExtension;
    }

    /**
     * @param \ACP3\Core\View\Event\TemplateEvent $templateEvent
     */
    public function onDisplayCaptcha(TemplateEvent $templateEvent)
    {
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true
            && $this->captchaExtension instanceof CaptchaExtensionInterface
        ) {
            $arguments = $templateEvent->getParameters();

            echo $this->captchaExtension->getCaptcha(
                isset($arguments['length']) ? $arguments['length'] : CaptchaExtensionInterface::CAPTCHA_DEFAULT_LENGTH,
                isset($arguments['input_id']) ? $arguments['input_id'] : CaptchaExtensionInterface::CAPTCHA_DEFAULT_INPUT_ID,
                isset($arguments['input_only']) ? $arguments['input_only'] : false,
                isset($arguments['path']) ? $arguments['path'] : ''
            );
        }
    }
}
