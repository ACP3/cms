<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Validation;

use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\ValidationRules\DateValidationRule;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Core\Validation\ValidationRules\MinLengthValidationRule;
use ACP3\Modules\ACP3\Articles\Validation\ValidationRules\LayoutExistsValidationRule;

class AdminFormValidation extends AbstractFormValidation
{
    private string $uriAlias = '';

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withUriAlias instead.
     */
    public function setUriAlias(string $uriAlias): self
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
            ->addConstraint(FormTokenValidationRule::class)
            ->addConstraint(
                InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'active',
                    'message' => $this->translator->t('articles', 'select_active'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            )
            ->addConstraint(
                DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date'),
                ]
            )
            ->addConstraint(
                MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('articles', 'title_to_short'),
                    'extra' => [
                        'length' => 3,
                    ],
                ]
            )
            ->addConstraint(
                MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('articles', 'text_to_short'),
                    'extra' => [
                        'length' => 3,
                    ],
                ]
            )
            ->addConstraint(
                LayoutExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'layout',
                    'message' => $this->translator->t('articles', 'select_layout'),
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
