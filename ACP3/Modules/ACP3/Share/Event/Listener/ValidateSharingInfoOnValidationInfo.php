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
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;

class ValidateSharingInfoOnValidationInfo
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
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;

    /**
     * ValidateSharingInfoOnValidationInfo constructor.
     *
     * @param \ACP3\Core\ACL                                  $acl
     * @param \ACP3\Core\I18n\Translator                      $translator
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices $socialServices
     */
    public function __construct(
        ACL $acl,
        Translator $translator,
        SocialServices $socialServices
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
        $this->socialServices = $socialServices;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function __invoke(FormValidationEvent $event)
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
                        'extra' => [
                            'haystack' => [0, 1],
                        ],
                    ]
                )
                ->addConstraint(
                    InArrayValidationRule::class,
                    [
                        'data' => $event->getFormData(),
                        'field' => 'share_customize_services',
                        'message' => $this->translator->t('share', 'select_customize_services'),
                        'extra' => [
                            'haystack' => [0, 1],
                        ],
                    ]
                );

            if (isset($event->getFormData()['share_customize_services'])
                && $event->getFormData()['share_customize_services'] == 1) {
                $event
                    ->getValidator()
                    ->addConstraint(
                        InArrayValidationRule::class,
                        [
                            'data' => $event->getFormData(),
                            'field' => 'share_services',
                            'message' => $this->translator->t('share', 'select_services'),
                            'extra' => [
                                'haystack' => $this->socialServices->getActiveServices(),
                            ],
                        ]
                    );
            }

            $event
                ->getValidator()
                ->addConstraint(
                    InArrayValidationRule::class,
                    [
                        'data' => $event->getFormData(),
                        'field' => 'share_ratings_active',
                        'message' => $this->translator->t('share', 'select_ratings_active'),
                        'extra' => [
                            'haystack' => [0, 1],
                        ],
                    ]
                );
        }
    }
}
