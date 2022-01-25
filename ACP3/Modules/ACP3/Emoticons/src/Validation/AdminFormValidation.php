<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Validation;

use ACP3\Core;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var array<string, mixed>|null
     */
    private array|null $file = null;
    /**
     * @var array<string, mixed>
     */
    private array $settings = [];

    private bool $fileRequired = false;

    /**
     * @param array<string, mixed>|null $file
     *
     * @return $this
     */
    public function setFile(?array $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @return $this
     */
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFileRequired(bool $fileRequired): self
    {
        $this->fileRequired = $fileRequired;

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
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'code',
                    'message' => $this->translator->t('emoticons', 'type_in_code'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->translator->t('emoticons', 'type_in_description'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::class,
                [
                    'data' => $this->file,
                    'field' => 'picture',
                    'message' => $this->translator->t('emoticons', 'invalid_image_selected'),
                    'extra' => [
                        'width' => $this->settings['width'],
                        'height' => $this->settings['height'],
                        'filesize' => $this->settings['filesize'],
                        'required' => $this->fileRequired,
                    ],
                ]
            );

        $this->validator->validate();
    }
}
