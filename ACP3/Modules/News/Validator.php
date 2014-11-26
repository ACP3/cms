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
     * @var Categories\Helpers
     */
    protected $categoriesHelpers;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param Core\Validator\Rules\Date $dateValidator
     * @param Core\Modules $modules
     * @param Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\Modules $modules,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->modules = $modules;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param array $formData
     * @param string $uriAlias
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $uriAlias = '')
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $this->errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('news', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $this->errors['text'] = $this->lang->t('news', 'text_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && $this->categoriesHelpers->categoryExists($formData['cat']) === false) {
            $this->errors['cat'] = $this->lang->t('news', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && $this->categoriesHelpers->categoryIsDuplicate($formData['cat_create'], 'news') === true) {
            $this->errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['link_title']) && (empty($formData['uri']) || $this->validate->isNumber($formData['target']) === false)) {
            $this->errors[] = $this->lang->t('news', 'complete_hyperlink_statements');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], $uriAlias) === true) {
            $this->errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
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
        if (empty($formData['dateformat']) ||
            ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')
        ) {
            $this->errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $this->errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['readmore']) ||
            ($formData['readmore'] != 1 && $formData['readmore'] != 0)
        ) {
            $this->errors['readmore'] = $this->lang->t('news', 'select_activate_readmore');
        }
        if ($this->validate->isNumber($formData['readmore_chars']) === false ||
            $formData['readmore_chars'] == 0
        ) {
            $this->errors['readmore-chars'] = $this->lang->t('news', 'type_in_readmore_chars');
        }
        if (!isset($formData['category_in_breadcrumb']) ||
            ($formData['category_in_breadcrumb'] != 1 && $formData['category_in_breadcrumb'] != 0)
        ) {
            $this->errors['category-in-breadcrumb'] = $this->lang->t('news', 'select_display_category_in_breadcrumb');
        }
        if ($this->modules->isActive('comments') === true &&
            (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)
        ) {
            $this->errors['comments'] = $this->lang->t('news', 'select_allow_comments');
        }

        $this->_checkForFailedValidation();
    }
}
