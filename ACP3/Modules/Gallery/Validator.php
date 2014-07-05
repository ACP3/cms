<?php
namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Gallery
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $file
     * @param array $settings
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreatePicture(array $file, array $settings)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($file['tmp_name'])) {
            $errors['file'] = $this->lang->t('gallery', 'no_picture_selected');
        }
        if (!empty($file['tmp_name']) &&
            (Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
                $_FILES['file']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file'] = $this->lang->t('gallery', 'invalid_image_selected');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN_PICTURE, $this->uri->id)))
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $file
     * @param array $settings
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEditPicture(array $file, array $settings)
    {
        $this->validateFormKey();

        $errors = array();
        if (!empty($file['tmp_name']) &&
            (Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
                $_FILES['file']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file'] = $this->lang->t('gallery', 'invalid_image_selected');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
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
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if (Core\Validate::isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $errors[] = $this->lang->t('gallery', 'select_use_overlay');
        }
        if (Core\Modules::isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $errors[] = $this->lang->t('gallery', 'select_allow_comments');
        }
        if (Core\Validate::isNumber($formData['thumbwidth']) === false || Core\Validate::isNumber($formData['width']) === false || Core\Validate::isNumber($formData['maxwidth']) === false) {
            $errors[] = $this->lang->t('gallery', 'invalid_image_width_entered');
        }
        if (Core\Validate::isNumber($formData['thumbheight']) === false || Core\Validate::isNumber($formData['height']) === false || Core\Validate::isNumber($formData['maxheight']) === false) {
            $errors[] = $this->lang->t('gallery', 'invalid_image_height_entered');
        }
        if (Core\Validate::isNumber($formData['filesize']) === false) {
            $errors['filesize'] = $this->lang->t('gallery', 'invalid_image_filesize_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 