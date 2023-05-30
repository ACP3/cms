<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MenuAlreadyExistsValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly MenuRepository $menuRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        $menuId = $extra['menu_id'] ?? 0;

        return $this->menuRepository->menuExistsByName($data, $menuId) === false;
    }
}
