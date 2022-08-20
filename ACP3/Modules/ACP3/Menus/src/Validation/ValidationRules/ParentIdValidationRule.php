<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ParentIdValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly MenuItemRepository $menuItemRepository)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkParentIdExists($data);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function checkParentIdExists(int $value): bool
    {
        if (empty($value)) {
            return true;
        }

        return $this->menuItemRepository->menuItemExists($value);
    }
}
