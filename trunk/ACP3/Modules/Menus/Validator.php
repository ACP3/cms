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

    public function __construct(
        Core\Lang $lang,
        Core\Validate $validate,
        Core\Modules $modules,
        Core\Request $request,
        Model $menuModel
    )
    {
        parent::__construct($lang, $validate);

        $this->modules = $modules;
        $this->request = $request;
        $this->menuModel = $menuModel;
    }

    /**
     * @param \ACP3\Modules\Articles\Helpers $articlesHelpers
     * @return $this
     */
    public function setArticlesHelpers(Articles\Helpers $articlesHelpers)
    {
        $this->articlesHelpers = $articlesHelpers;

        return $this;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name'])) {
            $errors['index-name'] = $this->lang->t('menus', 'type_in_index_name');
        }
        if (!isset($errors) && $this->menuModel->menuExistsByName($formData['index_name']) === true) {
            $errors['index-name'] = $this->lang->t('menus', 'index_name_unique');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('menus', 'menu_bar_title_to_short');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateItem(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->validate->isNumber($formData['mode']) === false) {
            $errors['mode'] = $this->lang->t('menus', 'select_page_type');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('menus', 'title_to_short');
        }
        if ($this->validate->isNumber($formData['block_id']) === false) {
            $errors['block-id'] = $this->lang->t('menus', 'select_menu_bar');
        }
        if (!empty($formData['parent']) && $this->validate->isNumber($formData['parent']) === false) {
            $errors['parent'] = $this->lang->t('menus', 'select_superior_page');
        }
        if (!empty($formData['parent']) && $this->validate->isNumber($formData['parent']) === true) {
            // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
            $parentBlock = $this->menuModel->getMenuItemBlockIdById($formData['parent']);
            if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                $errors['parent'] = $this->lang->t('menus', 'superior_page_not_allowed');
            }
        }
        if ($formData['display'] != 0 && $formData['display'] != 1) {
            $errors[] = $this->lang->t('menus', 'select_item_visibility');
        }
        if ($this->validate->isNumber($formData['target']) === false ||
            $formData['mode'] == 1 && $this->modules->isInstalled($formData['module']) === false ||
            $formData['mode'] == 2 && $this->validate->isInternalURI($formData['uri']) === false ||
            $formData['mode'] == 3 && empty($formData['uri']) ||
            $formData['mode'] == 4 && ($this->validate->isNumber($formData['articles']) === false || ($this->articlesHelpers && $this->articlesHelpers->articleExists($formData['articles']) === false))
        ) {
            $errors[] = $this->lang->t('menus', 'type_in_uri_and_target');
        }
        if ($formData['mode'] == 2 && !empty($formData['alias']) &&
            ($this->validate->isUriSafe($formData['alias']) === false || $this->validate->uriAliasExists($formData['alias'], $formData['uri']) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
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
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name'])) {
            $errors['index-name'] = $this->lang->t('menus', 'type_in_index_name');
        }
        if (!isset($errors) && $this->menuModel->menuExistsByName($formData['index_name'], $this->request->id) === true) {
            $errors['index-name'] = $this->lang->t('menus', 'index_name_unique');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('menus', 'menu_bar_title_to_short');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 