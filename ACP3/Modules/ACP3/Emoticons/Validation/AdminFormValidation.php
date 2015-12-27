<?php
namespace ACP3\Modules\ACP3\Emoticons\Validation;

use ACP3\Core;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Emoticons\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var null|array
     */
    protected $file;
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var bool
     */
    protected $fileRequired = false;

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
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

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
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'code',
                    'message' => $this->translator->t('emoticons', 'type_in_code')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->translator->t('emoticons', 'type_in_description')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $this->file,
                    'field' => 'picture',
                    'message' => $this->translator->t('emoticons', 'invalid_image_selected'),
                    'extra' => [
                        'width' => $this->settings['width'],
                        'height' => $this->settings['height'],
                        'filesize' => $this->settings['filesize'],
                        'required' => $this->fileRequired
                    ]
                ]);

        $this->validator->validate();
    }
}
