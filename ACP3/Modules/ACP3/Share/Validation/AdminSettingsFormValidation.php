<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Validation;

use ACP3\Core;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\Validator;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $availableServices;

    public function __construct(
        Translator $translator,
        Validator $validator,
        SocialServices $availableServices)
    {
        parent::__construct($translator, $validator);

        $this->availableServices = $availableServices;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'services',
                    'message' => $this->translator->t('share', 'select_services'),
                    'extra' => [
                        'haystack' => $this->availableServices->getAvailableServices(),
                    ],
                ]
            );

        if (isset($formData['services']) && \in_array('facebook', $formData['services']) === true) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'fb_app_id',
                        'message' => $this->translator->t('share', 'type_in_fb_app_id'),
                    ]
                )
                ->addConstraint(
                    Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'fb_secret',
                        'message' => $this->translator->t('share', 'type_in_fb_secret'),
                    ]
                );
        }

        $this->validator->validate();
    }
}
