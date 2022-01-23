<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\EventListener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Guestbooknewsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountExistsValidationRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGuestbookValidationCreateEventListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private SettingsInterface $settings, private Translator $translator)
    {
    }

    public function __invoke(FormValidationEvent $event): void
    {
        if (!$this->modules->isInstalled(NewsletterSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $formData = $event->getFormData();

        if (!empty($formData['mail'])) {
            $settings = $this->settings->getSettings(Schema::MODULE_NAME);

            if ($settings['newsletter_integration'] == 1 && isset($formData['subscribe_newsletter'])) {
                $event->getValidator()
                    ->addConstraint(
                        AccountExistsValidationRule::class,
                        [
                            'data' => $formData,
                            'field' => 'mail',
                            'message' => $this->translator->t('newsletter', 'account_exists'),
                        ]
                    );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'guestbook.validation.create' => '__invoke',
        ];
    }
}
