<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

class CategoryExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * CategoryExistsValidationRule constructor.
     *
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $categoryId = reset($field);
            $createCategory = next($field);

            return !empty($data[$createCategory]) || $this->categoryRepository->resultExists($data[$categoryId]);
        }

        return false;
    }
}
