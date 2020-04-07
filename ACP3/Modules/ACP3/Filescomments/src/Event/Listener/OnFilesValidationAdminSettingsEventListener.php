<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\Event\Listener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;

class OnFilesValidationAdminSettingsEventListener
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(FormValidationEvent $event)
    {
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
}
