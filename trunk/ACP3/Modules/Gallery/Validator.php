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
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Core\Request
     */
    protected $request;

    public function __construct(
        Core\Lang $lang,
        Core\Validate $validate,
        Core\Modules $modules,
        Core\Request $request
    )
    {
        parent::__construct($lang, $validate);

        $this->modules = $modules;
        $this->request = $request;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->validate->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) &&
            ($this->validate->isUriSafe($formData['alias']) === false || $this->validate->uriAliasExists($formData['alias']) === true)
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
            ($this->validate->isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
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
        if ($this->validate->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) &&
            ($this->validate->isUriSafe($formData['alias']) === false || $this->validate->uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN_PICTURE, $this->request->id)))
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
            ($this->validate->isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
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
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $errors[] = $this->lang->t('gallery', 'select_use_overlay');
        }
        if ($this->modules->isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $errors[] = $this->lang->t('gallery', 'select_allow_comments');
        }
        if ($this->validate->isNumber($formData['thumbwidth']) === false || $this->validate->isNumber($formData['width']) === false || $this->validate->isNumber($formData['maxwidth']) === false) {
            $errors[] = $this->lang->t('gallery', 'invalid_image_width_entered');
        }
        if ($this->validate->isNumber($formData['thumbheight']) === false || $this->validate->isNumber($formData['height']) === false || $this->validate->isNumber($formData['maxheight']) === false) {
            $errors[] = $this->lang->t('gallery', 'invalid_image_height_entered');
        }
        if ($this->validate->isNumber($formData['filesize']) === false) {
            $errors['filesize'] = $this->lang->t('gallery', 'invalid_image_filesize_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 