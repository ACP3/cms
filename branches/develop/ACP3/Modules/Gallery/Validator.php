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
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var Core\Validator\Rules\Mime
     */
    protected $mimeValidator;
    /**
     * @var Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Core\Request
     */
    protected $request;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param Core\Validator\Rules\Date $dateValidator
     * @param Core\Validator\Rules\Mime $mimeValidator
     * @param Core\Modules $modules
     * @param Core\Request $request
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\Validator\Rules\Mime $mimeValidator,
        Core\Modules $modules,
        Core\Request $request
    ) {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->mimeValidator = $mimeValidator;
        $this->modules = $modules;
        $this->request = $request;
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias']) === true) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $file
     * @param array $settings
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreatePicture(array $file, array $settings)
    {
        $this->validateFormKey();

        $errors = [];
        if (empty($file['tmp_name'])) {
            $errors['file'] = $this->lang->t('gallery', 'no_picture_selected');
        }
        if (!empty($file['tmp_name']) &&
            ($this->mimeValidator->isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
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
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN_GALLERY, $this->request->id))) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $file
     * @param array $settings
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEditPicture(array $file, array $settings)
    {
        $this->validateFormKey();

        $errors = [];
        if (!empty($file['tmp_name']) &&
            ($this->mimeValidator->isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
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
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = [];
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $errors['overlay'] = $this->lang->t('gallery', 'select_use_overlay');
        }
        if ($this->modules->isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $errors['comments'] = $this->lang->t('gallery', 'select_allow_comments');
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
