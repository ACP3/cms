<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnDisplayCaptchaListener implements EventSubscriberInterface
{
    public function __construct(private readonly ACL $acl, private readonly CaptchaExtensionInterface $captchaExtension)
    {
    }

    public function __invoke(TemplateEvent $templateEvent): void
    {
        if ($this->captchaExtension instanceof CaptchaExtensionInterface && $this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $arguments = $templateEvent->getParameters();

            $templateEvent->addContent(
                $this->captchaExtension->getCaptcha(
                    $arguments['length'] ?? CaptchaExtensionInterface::CAPTCHA_DEFAULT_LENGTH,
                    $arguments['input_id'] ?? CaptchaExtensionInterface::CAPTCHA_DEFAULT_INPUT_ID,
                    $arguments['input_only'] ?? false
                )
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'captcha.event.display_captcha' => '__invoke',
        ];
    }
}
