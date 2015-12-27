<?php
namespace ACP3\Modules\ACP3\Menus\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\MenuExistsValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\MenuNameValidationRule;

/**
 * Class MenuFormValidation
 * @package ACP3\Modules\ACP3\Menus\Validation
 */
class MenuFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var int
     */
    protected $menuId = 0;

    /**
     * @param int $menuId
     *
     * @return $this
     */
    public function setMenuId($menuId)
    {
        $this->menuId = (int)$menuId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                MenuNameValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'index_name',
                    'message' => $this->translator->t('menus', 'type_in_index_name')
                ])
            ->addConstraint(
                MenuExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'index_name',
                    'message' => $this->translator->t('menus', 'index_name_unique'),
                    'extra' => [
                        'menu_id' => $this->menuId
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('menus', 'menu_bar_title_to_short')
                ]);

        $this->validator->validate();
    }
}
