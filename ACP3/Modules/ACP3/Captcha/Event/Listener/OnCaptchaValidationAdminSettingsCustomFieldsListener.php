<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;

class OnCaptchaValidationAdminSettingsCustomFieldsListener
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * OnCaptchaValidationAdminSettingsCustomFieldsListener constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function validateRecaptchaSettings(FormValidationEvent $event)
    {
        $formData = $event->getFormData();

        if ($this->isRecaptcha($formData)) {
            $event->getValidator()
                ->addConstraint(NotEmptyValidationRule::class, [
                    'data' => $formData,
                    'field' => 'recaptcha_sitekey',
                    'message' => $this->translator->t('captcha', 'type_in_recaptcha_sitekey'),
                ])
                ->addConstraint(NotEmptyValidationRule::class, [
                    'data' => $formData,
                    'field' => 'recaptcha_secret',
                    'message' => $this->translator->t('captcha', 'type_in_recaptcha_secret'),
                ]);
        }
    }

    /**
     * @param array $formData
     *
     * @return bool
     */
    private function isRecaptcha(array $formData)
    {
        return !empty($formData['captcha']) && $formData['captcha'] === 'captcha.extension.recaptcha_captcha_extension';
    }
}
