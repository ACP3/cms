<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

/**
 * Class DuplicateCategoryValidationRule
 * @package ACP3\Modules\ACP3\Categories\Validation\ValidationRules
 */
class DuplicateCategoryValidationRule extends AbstractValidationRule
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
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        $params = array_merge([
            'module_id' => 0,
            'category_id' => ''
        ], $extra);

        return !$this->categoryRepository->resultIsDuplicate(
            $data,
            $params['module_id'],
            $params['category_id']
        );
    }
}
