<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
    private $acl;
    /**
     * @var CaptchaExtensionInterface
     */
    private $captchaExtension;

    public function __construct(ACL $acl, CaptchaExtensionInterface $captchaExtension = null)
    {
        $this->acl = $acl;
        $this->captchaExtension = $captchaExtension;
    }

    public function __invoke(TemplateEvent $templateEvent)
    {
        if ($this->captchaExtension instanceof CaptchaExtensionInterface && $this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $arguments = $templateEvent->getParameters();

            $templateEvent->addContent(
                $this->captchaExtension->getCaptcha(
                    $arguments['length'] ?? CaptchaExtensionInterface::CAPTCHA_DEFAULT_LENGTH,
                    $arguments['input_id'] ?? CaptchaExtensionInterface::CAPTCHA_DEFAULT_INPUT_ID,
                    $arguments['input_only'] ?? false,
                    $arguments['path'] ?? ''
                )
            );
        }
    }
}
