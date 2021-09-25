<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;

class CategoryExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $categoryId = reset($field);
            $createCategory = next($field);

            return !empty($data[$createCategory]) || $this->categoryRepository->resultExists((int) $data[$categoryId]);
        }

        return false;
    }
}
