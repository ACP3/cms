<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Validation;

use ACP3\Core;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    private string $uriAlias = '';

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withUriAlias instead.
     */
    public function setUriAlias(string $uriAlias): static
    {
        $this->uriAlias = $uriAlias;

        return $this;
    }

    public function withUriAlias(string $uriAlias): static
    {
        $clone = clone $this;
        $clone->uriAlias = $uriAlias;

        return $clone;
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
