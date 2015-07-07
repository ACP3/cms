<?php
namespace ACP3\Modules\ACP3\Files;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Files
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var Categories\Helpers
     */
    protected $categoriesHelpers;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param Core\Validator\Rules\Date $dateValidator
     * @param Core\Modules $modules
     * @param Core\Request $request
     * @param Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\Modules $modules,
        Core\Request $request,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->modules = $modules;
        $this->request = $request;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param array $formData
     * @param $file
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $file)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $this->errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['link-title'] = $this->lang->t('files', 'type_in_title');
        }
        if (isset($formData['external']) && (empty($file) || empty($formData['filesize']) || empty($formData['unit']))) {
            $this->errors['external'] = $this->lang->t('files', 'type_in_external_resource');
        }
        if (!isset($formData['external']) &&
            (empty($file['tmp_name']) || empty($file['size']) || $file['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['file-internal'] = $this->lang->t('files', 'select_internal_resource');
        }
        if (strlen($formData['text']) < 3) {
            $this->errors['text'] = $this->lang->t('files', 'description_to_short');
        }
        if (empty($formData['cat_create']) && $this->categoriesHelpers->categoryExists($formData['cat']) === false) {
            $this->errors['cat'] = $this->lang->t('files', 'select_category');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias']) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @param $file
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData, $file)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $this->errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['link-title'] = $this->lang->t('files', 'type_in_title');
        }
        if (isset($formData['external']) && (empty($file) || empty($formData['filesize']) || empty($formData['unit']))) {
            $this->errors['external'] = $this->lang->t('files', 'type_in_external_resource');
        }
        if (!isset($formData['external']) && !empty($file) && is_array($file) &&
            (empty($file['tmp_name']) || empty($file['size']) || $file['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['file-internal'] = $this->lang->t('files', 'select_internal_resource');
        }
        if (strlen($formData['text']) < 3) {
            $this->errors['text'] = $this->lang->t('files', 'description_to_short');
        }
        if (empty($formData['cat_create']) && $this->categoriesHelpers->categoryExists($formData['cat']) === false) {
            $this->errors['cat'] = $this->lang->t('files', 'select_category');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN, $this->request->id)) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $this->errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $this->errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if ($this->modules->isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $this->errors['comments'] = $this->lang->t('files', 'select_allow_comments');
        }

        $this->_checkForFailedValidation();
    }
}
