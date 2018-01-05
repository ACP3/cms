<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

class OnSeoValidationValidateUriAlias
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var ACLInterface
     */
    private $acl;

    /**
     * OnSeoValidationValidateUriAlias constructor.
     * @param ACLInterface $acl
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ACLInterface $acl,
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function validateUriAlias(FormValidationEvent $event)
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
}
