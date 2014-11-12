<?php
namespace ACP3\Modules\News;

use ACP3\Core;
use ACP3\Modules\Categories;

/**
 * Class Validator
 * @package ACP3\Modules\News
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
    )
    {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->modules = $modules;
        $this->request = $request;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('news', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('news', 'text_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && $this->categoriesHelpers->categoryExists($formData['cat']) === false) {
            $errors['cat'] = $this->lang->t('news', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && $this->categoriesHelpers->categoryIsDuplicate($formData['cat_create'], 'news') === true) {
            $errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['link_title']) && (empty($formData['uri']) || $this->validate->isNumber($formData['target']) === false)) {
            $errors[] = $this->lang->t('news', 'complete_hyperlink_statements');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias']) === true) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
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

        $errors = array();
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('news', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('news', 'text_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && $this->categoriesHelpers->categoryExists($formData['cat']) === false) {
            $errors['cat'] = $this->lang->t('news', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && $this->categoriesHelpers->categoryIsDuplicate($formData['cat_create'], 'news') === true) {
            $errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['link_title']) && (empty($formData['uri']) || $this->validate->isNumber($formData['target']) === false)) {
            $errors[] = $this->lang->t('news', 'complete_hyperlink_statements');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN, $this->request->id)) === true) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
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

        $errors = array();
        if (empty($formData['dateformat']) ||
            ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')
        ) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['readmore']) ||
            ($formData['readmore'] != 1 && $formData['readmore'] != 0)
        ) {
            $errors[] = $this->lang->t('news', 'select_activate_readmore');
        }
        if ($this->validate->isNumber($formData['readmore_chars']) === false ||
            $formData['readmore_chars'] == 0
        ) {
            $errors['readmore-chars'] = $this->lang->t('news', 'type_in_readmore_chars');
        }
        if (!isset($formData['category_in_breadcrumb']) ||
            ($formData['category_in_breadcrumb'] != 1 && $formData['category_in_breadcrumb'] != 0)
        ) {
            $errors[] = $this->lang->t('news', 'select_display_category_in_breadcrumb');
        }
        if ($this->modules->isActive('comments') === true &&
            (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)
        ) {
            $errors[] = $this->lang->t('news', 'select_allow_comments');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }


} 