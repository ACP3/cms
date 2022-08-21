<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Services;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Categories\Model\CategoriesModel;
use ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryUpsertService
{
    public function __construct(
        private readonly Modules $modules,
        private readonly Upload $categoriesUploadHelper,
        private readonly CategoriesModel $categoriesModel,
        private readonly AdminFormValidation $adminFormValidation)
    {
    }

    /**
     * @param array<string, mixed> $updatedData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function upsert(array $updatedData, ?UploadedFile $file = null, ?int $categoryId = null): int
    {
        $this->adminFormValidation
            ->withFile($file)
            ->withCategoryId($categoryId)
            ->validate($updatedData);

        if (empty($file) === false) {
            if ($categoryId !== null) {
                $category = $this->categoriesModel->getOneById($categoryId);
                $this->categoriesUploadHelper->removeUploadedFile($category['picture']);
            }

            $result = $this->categoriesUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
            $updatedData['picture'] = $result['name'];
        }

        return $this->categoriesModel->save($updatedData, $categoryId);
    }

    /**
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function createCategoryInline(string $categoryTitle, string $moduleName): int
    {
        $insertValues = [
            'title' => $categoryTitle,
            'module_id' => $this->modules->getModuleInfo($moduleName)['id'],
            'parent_id' => 0,
        ];

        return $this->upsert($insertValues);
    }
}
