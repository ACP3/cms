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
     * @var \ACP3\Core\Validator\Rules\Mime
     */
    protected $mimeValidator;

    /**
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     * @param \ACP3\Core\Validator\Rules\Mime $mimeValidator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Mime $mimeValidator
    )
    {
        parent::__construct($lang, $validate);

        $this->mimeValidator = $mimeValidator;
    }

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
        $this->validateFormKey();

        if (empty($formData['code'])) {
            $this->errors['code'] = $this->lang->t('emoticons', 'type_in_code');
        }
        if (empty($formData['description'])) {
            $this->errors['description'] = $this->lang->t('emoticons', 'type_in_description');
        }
        if ($this->mimeValidator->isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            $this->errors['picture'] = $this->lang->t('emoticons', 'invalid_image_selected');
        }

        $this->_checkForFailedValidation();
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
        $this->validateFormKey();

        if (empty($formData['code'])) {
            $this->errors['code'] = $this->lang->t('emoticons', 'type_in_code');
        }
        if (empty($formData['description'])) {
            $this->errors['description'] = $this->lang->t('emoticons', 'type_in_description');
        }
        if (!empty($file) && ($this->mimeValidator->isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false || $file['error'] !== UPLOAD_ERR_OK)) {
            $this->errors['picture'] = $this->lang->t('emoticons', 'invalid_image_selected');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->validate->isNumber($formData['width']) === false) {
            $this->errors['width'] = $this->lang->t('emoticons', 'invalid_image_width_entered');
        }
        if ($this->validate->isNumber($formData['height']) === false) {
            $this->errors['height'] = $this->lang->t('emoticons', 'invalid_image_height_entered');
        }
        if ($this->validate->isNumber($formData['filesize']) === false) {
            $this->errors['filesize'] = $this->lang->t('emoticons', 'invalid_image_filesize_entered');
        }

        $this->_checkForFailedValidation();
    }
}
