<?php
namespace ACP3\Modules\ACP3\Menus;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Menus
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;
    /**
     * @var \ACP3\Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;
    /**
     * @var Articles\Helpers
     */
    protected $articlesHelpers;

    /**
     * @param \ACP3\Core\Lang                           $lang
     * @param \ACP3\Core\Validator\Rules\Misc           $validate
     * @param \ACP3\Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param \ACP3\Core\Validator\Rules\Router         $routerValidator
     * @param \ACP3\Core\Modules                        $modules
     * @param \ACP3\Modules\ACP3\Menus\Model                 $menusModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Router $routerValidator,
        Core\Modules $modules,
        Model $menusModel
    ) {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->routerValidator = $routerValidator;
        $this->modules = $modules;
        $this->menusModel = $menusModel;
    }

    /**
     * @param Articles\Helpers $articlesHelpers
     * @return $this
     */
    public function setArticlesHelpers(Articles\Helpers $articlesHelpers)
    {
        $this->articlesHelpers = $articlesHelpers;

        return $this;
    }

    /**
     * @param array $formData
     * @param int $menuId
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $menuId = 0)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name'])) {
            $this->errors['index-name'] = $this->lang->t('menus', 'type_in_index_name');
        }
        if (!isset($this->errors) && $this->menusModel->menuExistsByName($formData['index_name'], $menuId) === true) {
            $this->errors['index-name'] = $this->lang->t('menus', 'index_name_unique');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('menus', 'menu_bar_title_to_short');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateItem(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->validate->isNumber($formData['mode']) === false) {
            $this->errors['mode'] = $this->lang->t('menus', 'select_page_type');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('menus', 'title_to_short');
        }
        if ($this->validate->isNumber($formData['block_id']) === false) {
            $this->errors['block-id'] = $this->lang->t('menus', 'select_menu_bar');
        }
        if (!empty($formData['parent_id']) && $this->validate->isNumber($formData['parent_id']) === false) {
            $this->errors['parent_id'] = $this->lang->t('menus', 'select_superior_page');
        }
        if (!empty($formData['parent_id']) && $this->validate->isNumber($formData['parent_id']) === true) {
            // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
            $parentBlock = $this->menusModel->getMenuItemBlockIdById($formData['parent_id']);
            if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                $this->errors['parent-id'] = $this->lang->t('menus', 'superior_page_not_allowed');
            }
        }
        if ($formData['display'] != 0 && $formData['display'] != 1) {
            $this->errors['display'] = $this->lang->t('menus', 'select_item_visibility');
        }
        if ($this->validate->isNumber($formData['target']) === false ||
            $formData['mode'] == 1 && $this->modules->isInstalled($formData['module']) === false ||
            $formData['mode'] == 2 && $this->routerValidator->isInternalURI($formData['uri']) === false ||
            $formData['mode'] == 3 && empty($formData['uri']) ||
            $formData['mode'] == 4 && ($this->validate->isNumber($formData['articles']) === false || ($this->articlesHelpers && $this->articlesHelpers->articleExists($formData['articles']) === false))
        ) {
            $this->errors['link'] = $this->lang->t('menus', 'type_in_uri_and_target');
        }
        if ($formData['mode'] == 2 && !empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], $formData['uri']) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
        }

        $this->_checkForFailedValidation();
    }
}
