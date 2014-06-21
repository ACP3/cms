<?php
namespace ACP3\Modules\Menus;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Menus
 */
class Validator extends Core\Validation\AbstractValidator
{
    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;
    /**
     * @var Model
     */
    protected $menuModel;

    public function __construct(Core\Lang $lang, Core\URI $uri, Model $menuModel) {
        parent::__construct($lang);

        $this->uri = $uri;
        $this->menuModel = $menuModel;
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
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
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
        if (Core\Validate::isNumber($formData['mode']) === false) {
            $errors['mode'] = $this->lang->t('menus', 'select_page_type');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('menus', 'title_to_short');
        }
        if (Core\Validate::isNumber($formData['block_id']) === false) {
            $errors['block-id'] = $this->lang->t('menus', 'select_menu_bar');
        }
        if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === false) {
            $errors['parent'] = $this->lang->t('menus', 'select_superior_page');
        }
        if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === true) {
            // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
            $parentBlock = $this->menuModel->getMenuItemBlockIdById($formData['parent']);
            if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                $errors['parent'] = $this->lang->t('menus', 'superior_page_not_allowed');
            }
        }
        if ($formData['display'] != 0 && $formData['display'] != 1) {
            $errors[] = $this->lang->t('menus', 'select_item_visibility');
        }
        if (Core\Validate::isNumber($formData['target']) === false ||
            $formData['mode'] == 1 && Core\Modules::isInstalled($formData['module']) === false ||
            $formData['mode'] == 2 && Core\Validate::isInternalURI($formData['uri']) === false ||
            $formData['mode'] == 3 && empty($formData['uri']) ||
            $formData['mode'] == 4 && (Core\Validate::isNumber($formData['articles']) === false || \ACP3\Modules\Articles\Helpers::articleExists($formData['articles']) === false)
        ) {
            $errors[] = $this->lang->t('menus', 'type_in_uri_and_target');
        }
        if ($formData['mode'] == 2 && (bool)CONFIG_SEO_ALIASES === true && !empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias'], $formData['uri']) === true)
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
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name'])) {
            $errors['index-name'] = $this->lang->t('menus', 'type_in_index_name');
        }
        if (!isset($errors) && $this->menuModel->menuExistsByName($formData['index_name'], $this->uri->id) === true) {
            $errors['index-name'] = $this->lang->t('menus', 'index_name_unique');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('menus', 'menu_bar_title_to_short');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

} 