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
    protected $menusModel;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;

    /**
     * @param \ACP3\Core\Lang                           $lang
     * @param \ACP3\Core\Validator\Rules\Misc           $validate
     * @param \ACP3\Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param \ACP3\Core\Validator\Rules\Date           $dateValidator
     * @param \ACP3\Core\ACL                            $acl
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\ACL $acl)
    {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->acl = $acl;
    }

    /**
     * @param \ACP3\Modules\Menus\Model $menusModel
     *
     * @return $this
     */
    public function setMenusModel(Menus\Model $menusModel)
    {
        $this->menusModel = $menusModel;

        return $this;
    }

    /**
     * @param array  $formData
     * @param string $uriAlias
     *
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
            $this->errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $this->errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if ($this->acl->hasPermission('admin/menus/items/create') === true && isset($formData['create']) === true) {
            if ($formData['create'] == 1) {
                if ($this->validate->isNumber($formData['block_id']) === false) {
                    $this->errors['block-id'] = $this->lang->t('menus', 'select_menu_bar');
                }
                if (!empty($formData['parent']) && $this->validate->isNumber($formData['parent']) === false) {
                    $this->errors['parent'] = $this->lang->t('menus', 'select_superior_page');
                }
                if (!empty($formData['parent']) && $this->validate->isNumber($formData['parent']) === true) {
                    // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
                    $parentBlock = $this->menusModel->getMenuItemBlockIdById($formData['parent']);
                    if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                        $this->errors['parent'] = $this->lang->t('menus', 'superior_page_not_allowed');
                    }
                }
                if ($formData['display'] != 0 && $formData['display'] != 1) {
                    $this->errors['display'] = $this->lang->t('menus', 'select_item_visibility');
                }
            }
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], $uriAlias) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
        }

        $this->_checkForFailedValidation();
    }
}