<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Validation;

use ACP3\Core;
use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files\Validation\ValidationRules\IsExternalFileValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    private string $uriAlias = '';

    private UploadedFile|string|null $file = null;

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withUriAlias instead.
     */
    public function setUriAlias(string $uriAlias): static
    {
        $this->uriAlias = $uriAlias;

        return $this;
    }

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withFile instead.
     */
    public function setFile(UploadedFile|string|null $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function withUriAlias(string $uriAlias): static
    {
        $clone = clone $this;
        $clone->uriAlias = $uriAlias;

        return $clone;
    }

    public function withFile(UploadedFile|string|null $file): static
    {
        $clone = clone $this;
        $clone->file = $file;

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
                    'field' => 'active',
                    'message' => $this->translator->t('files', 'select_active'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
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
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('files', 'type_in_title'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('files', 'description_to_short'),
                ]
            )
            ->addConstraint(
                IsExternalFileValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['external', 'filesize', 'unit'],
                    'message' => $this->translator->t('files', 'type_in_external_resource'),
                    'extra' => [
                        'file' => $this->file,
                    ],
                ]
            )
            ->addConstraint(
                Categories\Validation\ValidationRules\CategoryExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['cat', 'cat_create'],
                    'message' => $this->translator->t('files', 'select_category'),
                ]
            );

        if (!isset($formData['external'])) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\FileUploadValidationRule::class,
                    [
                        'data' => $this->file,
                        'field' => 'file_internal',
                        'message' => $this->translator->t('files', 'select_internal_resource'),
                        'extra' => [
                            'required' => empty($this->uriAlias),
                        ],
                    ]
                );
        }

        $this->validator->dispatchValidationEvent(
            'core.validation.form_extension',
            $formData,
            ['path' => $this->uriAlias]
        );

        $this->validator->validate();
    }
}
