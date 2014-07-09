<?php
namespace ACP3\Modules\Articles;

use ACP3\Core;
use ACP3\Modules\Menus;

class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Modules\Menus\Model
     */
    protected $menuModel;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;

    public function __construct(Core\Lang $lang, Core\Modules $modules, Core\URI $uri, Core\Validate $validate, Menus\Model $menuModel)
    {
        parent::__construct($lang, $validate);

        $this->uri = $uri;
        $this->modules = $modules;
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
        if ($this->validate->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if ($this->modules->hasPermission('admin/menus/index/create_item') === true && isset($formData['create']) === true) {
            if ($formData['create'] == 1) {
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
            }
        }
        if (!empty($formData['alias']) &&
            ($this->validate->isUriSafe($formData['alias']) === false || $this->validate->uriAliasExists($formData['alias']) === true)
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
        if ($this->validate->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if (!empty($formData['alias']) &&
            ($this->validate->isUriSafe($formData['alias']) === false || $this->validate->uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN, $this->uri->id)) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 