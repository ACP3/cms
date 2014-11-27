<?php
namespace ACP3\Modules\Menus;

use ACP3\Core;
use ACP3\Modules\Articles;

/**
 * Class Validator
 * @package ACP3\Modules\Menus
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var Model
     */
    protected $menuModel;
    /**
     * @var Articles\Helpers
     */
    protected $articlesHelpers;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param Core\Validator\Rules\Router $routerValidator
     * @param Core\Modules $modules
     * @param Model $menuModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Router $routerValidator,
        Core\Modules $modules,
        Model $menuModel
    ) {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->routerValidator = $routerValidator;
        $this->modules = $modules;
        $this->menuModel = $menuModel;
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
        if (!isset($this->errors) && $this->menuModel->menuExistsByName($formData['index_name'], $menuId) === true) {
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
        if (!empty($formData['parent']) && $this->validate->isNumber($formData['parent']) === false) {
            $this->errors['parent'] = $this->lang->t('menus', 'select_superior_page');
        }
        if (!empty($formData['parent']) && $this->validate->isNumber($formData['parent']) === true) {
            // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
            $parentBlock = $this->menuModel->getMenuItemBlockIdById($formData['parent']);
            if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                $this->errors['parent'] = $this->lang->t('menus', 'superior_page_not_allowed');
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
            $this->errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        $this->_checkForFailedValidation();
    }
}
