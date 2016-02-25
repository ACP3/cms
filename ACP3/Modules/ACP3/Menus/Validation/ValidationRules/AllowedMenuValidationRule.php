<?php
namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Model\MenuItemRepository;

/**
 * Class AllowedMenuValidationRule
 * @package ACP3\Modules\ACP3\Menus\Validation\ValidationRules
 */
class AllowedMenuValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * AllowedMenuValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     */
    public function __construct(MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $parentId = reset($field);
            $blockId = next($field);

            return $this->checkIsAllowedMenu($data[$parentId], $data[$blockId]);
        }

        return false;
    }

    /**
     * @param int $parentId
     * @param int $menuId
     *
     * @return bool
     */
    protected function checkIsAllowedMenu($parentId, $menuId)
    {
        if (empty($parentId)) {
            return true;
        }

        $parentMenuId = $this->menuItemRepository->getMenuItemMenuIdById($parentId);

        return (!empty($parentMenuId) && $parentMenuId == $menuId);
    }
}
