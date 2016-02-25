<?php
namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Model\MenuRepository;

/**
 * Class MenuExistsValidationRule
 * @package ACP3\Modules\ACP3\Menus\Validation\ValidationRules
 */
class MenuExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuRepository;

    /**
     * MenuExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuRepository $menuRepository
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

        return $this->menuRepository->menuExistsByName($data, isset($extra['menu_id']) ? $extra['menu_id'] : 0) === false;
    }
}
