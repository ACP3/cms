<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;

class AllowedSuperiorCategoryValidationRule extends AbstractValidationRule
{
    /**
     * @var CategoriesRepository
     */
    protected $categoriesRepository;

    /**
     * AllowedSuperiorCategoryValidationRule constructor.
     *
     * @param CategoriesRepository $categoriesRepository
     */
    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $parentId = \reset($field);
            $blockId = \next($field);

            return $this->checkIsAllowedMenu($data[$parentId], $data[$blockId]);
        }

        return false;
    }

    /**
     * @param int $parentId
     * @param int $categoryId
     *
     * @return bool
     */
    protected function checkIsAllowedMenu($parentId, $categoryId)
    {
        if (empty($parentId)) {
            return true;
        }

        $parentCategoryId = $this->categoriesRepository->getModuleIdByCategoryId($parentId);

        return !empty($parentCategoryId) && $parentCategoryId == $categoryId;
    }
}
