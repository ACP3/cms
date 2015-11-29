<?php
namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Emoticons
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array      $formData
     * @param null|array $file
     * @param array      $settings
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $file, array $settings)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'code',
                    'message' => $this->lang->t('emoticons', 'type_in_code')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->lang->t('emoticons', 'type_in_description')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $file,
                    'field' => 'picture',
                    'message' => $this->lang->t('emoticons', 'invalid_image_selected'),
                    'extra' => $settings
                ]);

        $this->validator->validate();
    }

    /**
     * @param array      $formData
     * @param null|array $file
     * @param array      $settings
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData, $file, array $settings)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'code',
                    'message' => $this->lang->t('emoticons', 'type_in_code')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->lang->t('emoticons', 'type_in_description')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $file,
                    'field' => 'picture',
                    'message' => $this->lang->t('emoticons', 'invalid_image_selected'),
                    'extra' => [
                        'width' => $settings['width'],
                        'height' => $settings['height'],
                        'filesize' => $settings['filesize'],
                        'required' => false
                    ]
                ]);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->lang->t('emoticons', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->lang->t('emoticons', 'invalid_image_height_entered')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'filesize',
                    'message' => $this->lang->t('emoticons', 'invalid_image_filesize_entered')
                ]);

        $this->validator->validate();
    }
}
