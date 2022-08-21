<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Validation;

use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\InternalUriValidationRule;
use ACP3\Modules\ACP3\Share\Validation\Event\SharingInfoFormValidationEvent;

class AdminFormValidation extends AbstractFormValidation
{
    private string $uri = '';

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withUri instead.
     */
    public function setUriAlias(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    public function withUri(string $uri): static
    {
        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(FormTokenValidationRule::class)
            ->addConstraint(
                InternalUriValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'uri',
                    'message' => $this->translator->t('share', 'type_in_valid_resource'),
                ]
            );

        $this->validator->dispatchValidationEvent(
            SharingInfoFormValidationEvent::class,
            $formData,
            ['path' => $this->uri]
        );

        $this->validator->validate();
    }
}
