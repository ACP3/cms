<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ValidateSharingInfoOnValidationInfo implements EventSubscriberInterface
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

    public function __construct(
        ACL $acl,
        Translator $translator,
        SocialServices $socialServices
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
        $this->socialServices = $socialServices;
    }

    public function __invoke(FormValidationEvent $event)
    {
        if ($this->acl->hasPermission('admin/share/index/create')) {
            if (isset($event->getFormData()['share_active'], $event->getFormData()['share_customize_services'])) {
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
            }

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

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.validation.form_extension' => '__invoke',
            'share.validation.validate_sharing_info' => '__invoke',
        ];
    }
}
