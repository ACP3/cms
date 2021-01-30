<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\EventListener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnCaptchaValidationValidateCaptchaListener implements EventSubscriberInterface
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(FormValidationEvent $event)
    {
        $event
            ->getValidator()
            ->addConstraint(
                CaptchaValidationRule::class,
                [
                    'data' => $event->getFormData(),
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered'),
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'captcha.validation.validate_captcha' => '__invoke',
        ];
    }
}
