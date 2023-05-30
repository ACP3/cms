<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Validation;

use ACP3\Core;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Validation\Validator;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    private ?UploadedFile $file = null;

    private bool $fileRequired = false;

    public function __construct(Core\I18n\Translator $translator, Validator $validator, private readonly SettingsInterface $settings)
    {
        parent::__construct($translator, $validator);
    }

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withFile instead.
     */
    public function setFile(?UploadedFile $file): static
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0.
     */
    public function setSettings(array $settings): static
    {
        return $this;
    }

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withFile instead.
     */
    public function setFileRequired(bool $fileRequired): static
    {
        $this->fileRequired = $fileRequired;

        return $this;
    }

    public function withFile(?UploadedFile $file, bool $isRequired): static
    {
        $clone = clone $this;
        $clone->file = $file;
        $clone->fileRequired = $isRequired;

        return $clone;
    }

    public function validate(array $formData): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

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
                        'width' => $settings['width'],
                        'height' => $settings['height'],
                        'filesize' => $settings['filesize'],
                        'required' => $this->fileRequired,
                    ],
                ]
            );

        $this->validator->validate();
    }
}
