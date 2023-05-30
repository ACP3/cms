<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

class GalleryFormValidation extends Core\Validation\AbstractFormValidation
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

    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('gallery', 'type_in_gallery_title'),
                ]
            );

        $this->validator->dispatchValidationEvent(
            'core.validation.form_extension',
            $formData,
            ['path' => $this->uriAlias]
        );

        $this->validator->validate();
    }
}
