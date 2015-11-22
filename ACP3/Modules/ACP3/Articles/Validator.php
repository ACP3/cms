<?php
namespace ACP3\Modules\ACP3\Articles;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

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
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * @param \ACP3\Core\Lang                           $lang
     * @param \ACP3\Core\Validator\Validator            $validator
     * @param \ACP3\Core\Validator\Rules\Misc           $validate
     * @param \ACP3\Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param \ACP3\Core\Validator\Rules\Date           $dateValidator
     * @param \ACP3\Core\ACL                            $acl
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\ACL $acl)
    {
        parent::__construct($lang, $validate);

        $this->validator = $validator;
        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->acl = $acl;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     *
     * @return $this
     */
    public function setMenuItemRepository(Menus\Model\MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;

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
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\MinLengthValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('articles', 'title_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\MinLengthValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->lang->t('articles', 'text_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ]
            );
        if ($this->acl->hasPermission('admin/menus/items/create') === true && isset($formData['create']) === true) {
            $this->validateMenuItem($formData);
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], $uriAlias) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
        }

        $this->validator->validate();

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     */
    protected function validateMenuItem(array $formData)
    {
        if ($formData['create'] == 1) {
            $this->validator->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'block_id',
                    'message' => $this->lang->t('menus', 'select_menu_bar')
                ]
            );
            if (!empty($formData['parent_id']) && $this->validate->isNumber($formData['parent_id']) === false) {
                $this->errors['parent-id'] = $this->lang->t('menus', 'select_superior_page');
            }
            if (!empty($formData['parent_id']) && $this->validate->isNumber($formData['parent_id']) === true) {
                // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
                $parentBlock = $this->menuItemRepository->getMenuItemBlockIdById($formData['parent_id']);
                if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                    $this->errors['parent-id'] = $this->lang->t('menus', 'superior_page_not_allowed');
                }
            }
            if ($formData['display'] != 0 && $formData['display'] != 1) {
                $this->errors['display'] = $this->lang->t('menus', 'select_item_visibility');
            }
        }
    }
}