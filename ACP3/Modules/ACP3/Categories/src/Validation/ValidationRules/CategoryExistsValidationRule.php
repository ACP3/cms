<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryExistsValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \is_array($field)) {
            $categoryId = reset($field);
            $createCategory = next($field);

            return !empty($data[$createCategory]) || (isset($data[$categoryId]) && $this->categoryRepository->resultExists((int) $data[$categoryId]));
        }

        return false;
    }
}
