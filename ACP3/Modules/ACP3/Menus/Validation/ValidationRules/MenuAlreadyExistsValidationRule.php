<?php
namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;

/**
 * Class MenuAlreadyExistsValidationRule
 * @package ACP3\Modules\ACP3\Menus\Validation\ValidationRules
 */
class MenuAlreadyExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository
     */
    protected $menuRepository;

    /**
     * MenuExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        $menuId = isset($extra['menu_id']) ? $extra['menu_id'] : 0;
        return $this->menuRepository->menuExistsByName($data, $menuId) === false;
    }
}
