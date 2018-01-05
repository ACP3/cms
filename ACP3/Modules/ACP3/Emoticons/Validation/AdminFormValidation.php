<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Validation;

use ACP3\Core;
use ACP3\Core\Validation\Validator;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * @var null|array
     */
    protected $file;
    /**
     * @var bool
     */
    protected $fileRequired = false;

    public function __construct(
        Core\I18n\TranslatorInterface $translator,
        Validator $validator,
        Core\Settings\SettingsInterface $settings
    ) {
        parent::__construct($translator, $validator);

        $this->settings = $settings;
    }

    /**
     * @param array|null $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param bool $fileRequired
     *
     * @return $this
     */
    public function setFileRequired($fileRequired)
    {
        $this->fileRequired = (bool)$fileRequired;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
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
