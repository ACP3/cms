<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\StructuredDataValidationRule;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnSeoValidationValidateUriAlias implements EventSubscriberInterface
{
    public function __construct(private ACL $acl, private Translator $translator)
    {
    }

    public function __invoke(FormValidationEvent $event): void
    {
        if ($this->acl->hasPermission('admin/seo/index/create')) {
            $event
                ->getValidator()
                ->addConstraint(
                    UriAliasValidationRule::class,
                    [
                        'data' => $event->getFormData(),
                        'field' => 'alias',
                        'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                        'extra' => $event->getExtra(),
                    ]
                );
        }

        $event->getValidator()
            ->addConstraint(
                StructuredDataValidationRule::class,
                [
                    'data' => $event->getFormData(),
                    'field' => 'seo_structured_data',
                    'message' => $this->translator->t('seo', 'type_in_valid_json_ld_structured_data'),
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.validation.form_extension' => '__invoke',
            'seo.validation.validate_uri_alias' => '__invoke',
        ];
    }
}
