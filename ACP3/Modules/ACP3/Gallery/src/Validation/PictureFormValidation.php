<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery\Validation\ValidationRules\GalleryExistsValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureFormValidation extends Core\Validation\AbstractFormValidation
{
    private bool $fileRequired = false;

    private ?UploadedFile $file = null;

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withFile instead.
     */
    public function setFileRequired(bool $fileRequired): self
    {
        $this->fileRequired = $fileRequired;

        return $this;
    }

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withFile instead.
     */
    public function setFile(?UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function withFile(?UploadedFile $file, bool $isRequired): static
    {
        $clone = clone $this;
        $clone->file = $file;
        $clone->fileRequired = $isRequired;

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
                Core\Validation\ValidationRules\PictureValidationRule::class,
                [
                    'data' => $this->file,
                    'field' => 'file',
                    'message' => $this->translator->t('gallery', 'invalid_image_selected'),
                    'extra' => [
                        'required' => $this->fileRequired,
                    ],
                ]
            );

        if (!empty($formData['gallery_id'])) {
            $this->validator
                ->addConstraint(
                    GalleryExistsValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'gallery_id',
                        'message' => $this->translator->t('gallery', 'invalid_gallery_selected'),
                    ]
                );
        }

        $this->validator->validate();
    }
}
