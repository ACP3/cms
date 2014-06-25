<?php
namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Files
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;

    public function __construct(Core\Lang $lang, Core\URI $uri)
    {
        parent::__construct($lang);

        $this->uri = $uri;
    }

    /**
     * @param array $formData
     * @param $file
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $file)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['link-title'] = $this->lang->t('files', 'type_in_title');
        }
        if (isset($formData['external']) && (empty($file) || empty($formData['filesize']) || empty($formData['unit']))) {
            $errors['external'] = $this->lang->t('files', 'type_in_external_resource');
        }
        if (!isset($formData['external']) &&
            (empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file-internal'] = $this->lang->t('files', 'select_internal_resource');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('files', 'description_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $this->lang->t('files', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'files') === true) {
            $errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['alias']) && (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * @param array $formData
     * @param $file
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData, $file)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['link-title'] = $this->lang->t('files', 'type_in_title');
        }
        if (isset($formData['external']) && (empty($file) || empty($formData['filesize']) || empty($formData['unit']))) {
            $errors['external'] = $this->lang->t('files', 'type_in_external_resource');
        }
        if (!isset($formData['external']) && isset($file) && is_array($file) &&
            (empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file-internal'] = $this->lang->t('files', 'select_internal_resource');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('files', 'description_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $this->lang->t('files', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'files') === true) {
            $errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN, $this->uri->id)) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
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
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if (Core\Validate::isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (Core\Modules::isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $errors[] = $this->lang->t('files', 'select_allow_comments');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

} 