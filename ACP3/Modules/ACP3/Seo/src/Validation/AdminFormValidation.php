<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation;

use ACP3\Core;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    private string $uriAlias = '';

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
                    'field' => 'seo_robots',
                    'message' => $this->translator->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [0, 1, 2, 3, 4],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'uri',
                    'message' => $this->translator->t('seo', 'type_in_valid_resource'),
                ]
            );

        $this->validator->dispatchValidationEvent(
            'seo.validation.validate_uri_alias',
            $formData,
            ['path' => $this->uriAlias]
        );

        $this->validator->validate();
    }
}
