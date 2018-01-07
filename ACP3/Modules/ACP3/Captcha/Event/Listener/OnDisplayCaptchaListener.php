<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;

class OnDisplayCaptchaListener
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var CaptchaExtensionInterface
     */
    private $captchaExtension;

    /**
     * OnDisplayCaptchaListener constructor.
     *
     * @param ACLInterface                   $acl
     * @param CaptchaExtensionInterface|null $captchaExtension
     */
    public function __construct(ACLInterface $acl, CaptchaExtensionInterface $captchaExtension = null)
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
                $arguments['length'] ?? CaptchaExtensionInterface::CAPTCHA_DEFAULT_LENGTH,
                $arguments['input_id'] ?? CaptchaExtensionInterface::CAPTCHA_DEFAULT_INPUT_ID,
                $arguments['input_only'] ?? false,
                $arguments['path'] ?? ''
            );
        }
    }
}
