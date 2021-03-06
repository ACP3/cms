<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnSeoValidationValidateUriAlias implements EventSubscriberInterface
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var ACL
     */
    private $acl;

    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
    }

    public function __invoke(FormValidationEvent $event)
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
