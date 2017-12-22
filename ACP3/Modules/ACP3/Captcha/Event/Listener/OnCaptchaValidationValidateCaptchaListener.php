<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;

class OnCaptchaValidationValidateCaptchaListener
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * OnCaptchaValidationValidateCaptcha constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function validateCaptcha(FormValidationEvent $event)
    {
        $event
            ->getValidator()
            ->addConstraint(
                CaptchaValidationRule::class,
                [
                    'data' => $event->getFormData(),
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ]
            );
    }
}
