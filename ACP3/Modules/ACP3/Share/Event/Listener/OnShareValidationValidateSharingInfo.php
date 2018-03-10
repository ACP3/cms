<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;

class OnShareValidationValidateSharingInfo
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var ACL
     */
    private $acl;

    /**
     * OnSeoValidationValidateUriAlias constructor.
     *
     * @param ACL        $acl
     * @param Translator $translator
     */
    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function validateUriAlias(FormValidationEvent $event)
    {
        if ($this->acl->hasPermission('admin/share/index/create')) {
            $event
                ->getValidator()
                ->addConstraint(
                    InArrayValidationRule::class,
                    [
                        'data' => $event->getFormData(),
                        'field' => 'share_active',
                        'message' => $this->translator->t('share', 'select_sharing_active'),
                        'extra' => [0, 1],
                    ]
                );
        }
    }
}
