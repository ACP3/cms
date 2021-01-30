<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\EventListener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Filescomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnFilesValidationAdminSettingsEventListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, Translator $translator)
    {
        $this->translator = $translator;
        $this->modules = $modules;
    }

    public function __invoke(FormValidationEvent $event)
    {
        if (!$this->modules->isActive(CommentsSchema::MODULE_NAME) || !$this->modules->isActive(Schema::MODULE_NAME)) {
            return;
        }

        $event->getValidator()
            ->addConstraint(
                InArrayValidationRule::class,
                [
                    'data' => $event->getFormData(),
                    'field' => 'comments',
                    'message' => $this->translator->t('filescomments', 'select_allow_comments'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'files.validation.admin_settings' => '__invoke',
        ];
    }
}
