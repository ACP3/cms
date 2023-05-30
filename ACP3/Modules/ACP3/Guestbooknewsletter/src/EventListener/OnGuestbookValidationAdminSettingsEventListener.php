<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\EventListener;

use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Guestbooknewsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGuestbookValidationAdminSettingsEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly Translator $translator)
    {
    }

    public function __invoke(FormValidationEvent $event): void
    {
        if (!$this->modules->isInstalled(NewsletterSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $event->getValidator()
            ->addConstraint(
                InArrayValidationRule::class,
                [
                    'data' => $event->getFormData(),
                    'field' => 'newsletter_integration',
                    'message' => $this->translator->t('guestbooknewsletter', 'select_newsletter_integration'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'guestbook.validation.admin_settings' => '__invoke',
        ];
    }
}
