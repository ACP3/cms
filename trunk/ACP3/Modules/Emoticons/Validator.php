<?php
namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Emoticons
 */
class Validator extends Core\Validation\AbstractValidator
{
    /**
     * @param array $formData
     * @param $file
     * @param array $settings
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $file, array $settings)
    {
        $this->validateFormKey();

        if (empty($formData['code'])) {
            $errors['code'] = $this->lang->t('emoticons', 'type_in_code');
        }
        if (empty($formData['description'])) {
            $errors['description'] = $this->lang->t('emoticons', 'type_in_description');
        }
        if (Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
            $_FILES['picture']['error'] !== UPLOAD_ERR_OK
        ) {
            $errors['picture'] = $this->lang->t('emoticons', 'invalid_image_selected');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * @param array $formData
     * @param $file
     * @param array $settings
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData, $file, array $settings)
    {
        $this->validateFormKey();

        if (empty($formData['code'])) {
            $errors['code'] = $this->lang->t('emoticons', 'type_in_code');
        }
        if (empty($formData['description'])) {
            $errors['description'] = $this->lang->t('emoticons', 'type_in_description');
        }
        if (!empty($file) && (Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false || $_FILES['picture']['error'] !== UPLOAD_ERR_OK)) {
            $errors['picture'] = $this->lang->t('emoticons', 'invalid_image_selected');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::isNumber($formData['width']) === false) {
            $errors['width'] = $this->lang->t('emoticons', 'invalid_image_width_entered');
        }
        if (Core\Validate::isNumber($formData['height']) === false) {
            $errors['height'] = $this->lang->t('emoticons', 'invalid_image_height_entered');
        }
        if (Core\Validate::isNumber($formData['filesize']) === false) {
            $errors['filesize'] = $this->lang->t('emoticons', 'invalid_image_filesize_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }


} 