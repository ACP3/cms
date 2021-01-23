<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles\Validation\ValidationRules\LayoutExistsValidationRule;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    private $uriAlias = '';

    /**
     * @return $this
     */
    public function setUriAlias(string $uriAlias): self
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
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'active',
                    'message' => $this->translator->t('articles', 'select_active'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
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
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
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
