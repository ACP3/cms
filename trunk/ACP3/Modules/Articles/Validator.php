<?php
namespace ACP3\Modules\Articles;

use ACP3\Core;
use ACP3\Modules\Menus;

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
     * @var \ACP3\Modules\Menus\Model
     */
    protected $menuModel;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\Modules $modules,
        Core\Request $request,
        Menus\Model $menuModel)
    {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->request = $request;
        $this->modules = $modules;
        $this->menuModel = $menuModel;
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
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if ($this->acl->hasPermission('admin/menus/items/create') === true && isset($formData['create']) === true) {
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
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN, $this->request->id)) === true) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 