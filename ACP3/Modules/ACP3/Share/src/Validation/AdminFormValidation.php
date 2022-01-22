<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Validation;

use ACP3\Core;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $uriAlias = '';

    /**
     * @return $this
     */
    public function setUriAlias(string $uriAlias)
    {
        $this->uriAlias = $uriAlias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'uri',
                    'message' => $this->translator->t('share', 'type_in_valid_resource'),
                ]
            );

        $this->validator->dispatchValidationEvent(
            'share.validation.validate_sharing_info',
            $formData,
            ['path' => $this->uriAlias]
        );

        $this->validator->validate();
    }
}
