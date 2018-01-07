<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository;

class MenuAlreadyExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository
     */
    protected $menuRepository;

    /**
     * MenuExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository $menuRepository
     */
    public function __construct(MenusRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        $menuId = $extra['menu_id'] ?? 0;

        return $this->menuRepository->menuExistsByName($data, $menuId) === false;
    }
}
