<?php
namespace ACP3\Modules\News;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\News
 */
class Validator extends Core\Validation\AbstractValidator
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
            $errors['title'] = $this->lang->t('news', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('news', 'text_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $this->lang->t('news', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'news') === true) {
            $errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['link_title']) && (empty($formData['uri']) || Core\Validate::isNumber($formData['target']) === false)) {
            $errors[] = $this->lang->t('news', 'complete_hyperlink_statements');
        }
        if (!empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)
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
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('news', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('news', 'text_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $this->lang->t('news', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'news') === true) {
            $errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['link_title']) && (empty($formData['uri']) || Core\Validate::isNumber($formData['target']) === false)) {
            $errors[] = $this->lang->t('news', 'complete_hyperlink_statements');
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
        if (empty($formData['dateformat']) ||
            ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')
        ) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if (Core\Validate::isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['readmore']) ||
            ($formData['readmore'] != 1 && $formData['readmore'] != 0)
        ) {
            $errors[] = $this->lang->t('news', 'select_activate_readmore');
        }
        if (Core\Validate::isNumber($formData['readmore_chars']) === false ||
            $formData['readmore_chars'] == 0
        ) {
            $errors['readmore-chars'] = $this->lang->t('news', 'type_in_readmore_chars');
        }
        if (!isset($formData['category_in_breadcrumb']) ||
            ($formData['category_in_breadcrumb'] != 1 && $formData['category_in_breadcrumb'] != 0)
        ) {
            $errors[] = $this->lang->t('news', 'select_display_category_in_breadcrumb');
        }
        if (Core\Modules::isActive('comments') === true &&
            (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)
        ) {
            $errors[] = $this->lang->t('news', 'select_allow_comments');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }


} 