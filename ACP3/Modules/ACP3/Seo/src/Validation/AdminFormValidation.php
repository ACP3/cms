<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation;

use ACP3\Core;
use ACP3\Core\SEO\Enum\MetaRobotsEnum;

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
                        'haystack' => [0, ...MetaRobotsEnum::values()],
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
