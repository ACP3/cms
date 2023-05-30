<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\EventListener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnCaptchaValidationAdminSettingsCustomFieldsListener implements EventSubscriberInterface
{
    public function __construct(private readonly Translator $translator)
    {
    }

    public function __invoke(FormValidationEvent $event): void
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
     * @param array<string, mixed> $formData
     */
    private function isRecaptcha(array $formData): bool
    {
        return !empty($formData['captcha']) && $formData['captcha'] === 'captcha.extension.recaptcha_captcha_extension';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'captcha.validation.admin_settings.custom_fields' => '__invoke',
        ];
    }
}
