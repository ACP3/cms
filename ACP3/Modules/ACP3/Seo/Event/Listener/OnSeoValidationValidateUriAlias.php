<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

class OnSeoValidationValidateUriAlias
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * OnSeoValidationValidateUriAlias constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function validateUriAlias(FormValidationEvent $event)
    {
        $event
            ->getValidator()
            ->addConstraint(
                UriAliasValidationRule::class,
                [
                    'data' => $event->getFormData(),
                    'field' => 'alias',
                    'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => $event->getExtra()
                ]
            );
    }
}
