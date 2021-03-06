<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;

class ParentIdValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    private $menuItemRepository;

    public function __construct(MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkParentIdExists($data);
    }

    /**
     * @param string $value
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function checkParentIdExists($value): bool
    {
        if (empty($value)) {
            return true;
        }

        return $this->menuItemRepository->menuItemExists((int) $value);
    }
}
