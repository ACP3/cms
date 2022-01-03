<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AllowedSuperiorCategoryValidationRule extends AbstractValidationRule
{
    public function __construct(private CategoryRepository $categoriesRepository)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \is_array($field)) {
            $parentId = reset($field);
            $blockId = next($field);

            return $this->checkIsAllowedMenu($data[$parentId], $data[$blockId]);
        }

        return false;
    }

    /**
     * @param int $parentId
     * @param int $categoryId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
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
