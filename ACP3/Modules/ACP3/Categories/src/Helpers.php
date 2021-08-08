<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\CategoriesModel;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;

class Helpers
{
    /**
     * @var Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Repository\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoriesModel
     */
    private $categoriesModel;

    public function __construct(
        Core\ACL $acl,
        Core\I18n\Translator $translator,
        Core\Modules $modules,
        Core\Http\RequestInterface $request,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\Secure $secureHelper,
        CategoriesModel $categoriesModel,
        CategoryRepository $categoryRepository
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
        $this->modules = $modules;
        $this->request = $request;
        $this->formsHelper = $formsHelper;
        $this->secureHelper = $secureHelper;
        $this->categoryRepository = $categoryRepository;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * Erzeugt eine neue Kategorie und gibt ihre ID zurück.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function categoriesCreate(string $categoryTitle, string $moduleName): int
    {
        $moduleInfo = $this->modules->getModuleInfo($moduleName);
        if ($this->categoryRepository->resultIsDuplicate($categoryTitle, $moduleInfo['id'], 0) === false) {
            $insertValues = [
                'title' => $this->secureHelper->strEncode($categoryTitle),
                'module_id' => $moduleInfo['id'],
                'parent_id' => 0,
            ];

            return $this->categoriesModel->save($insertValues);
        }

        return $this->categoryRepository->getOneByTitleAndModule($categoryTitle, $moduleName)['id'];
    }

    /**
     * Listet alle Kategorien eines Moduls auf.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function categoriesList(
        string $moduleName,
        ?int $categoryId = null,
        bool $categoryCreate = false,
        string $formFieldName = 'cat',
        ?string $customText = null
    ): array {
        $categories = $this->categoryRepository->getAllByModuleName($moduleName);
        foreach ($categories as &$category) {
            $category['title'] = str_repeat('&nbsp;&nbsp;', $category['level']) . $category['title'];
            $category['selected'] = $this->formsHelper->selectEntry(
                $formFieldName,
                $category['id'],
                $categoryId
            );
        }

        return [
            'custom_text' => $customText ?: $this->translator->t('system', 'pls_select'),
            'name' => $formFieldName,
            'categories' => $categories,
            'create' => $this->addCreateCategoryFormFields($categoryCreate, $formFieldName),
        ];
    }

    private function addCreateCategoryFormFields(bool $categoryCreate, string $formFieldName): array
    {
        $formFields = [];
        if ($categoryCreate === true && $this->acl->hasPermission('admin/categories/index/create') === true) {
            $formFields['name'] = $formFieldName . '_create';
            $formFields['value'] = $this->request->getPost()->get('create', ['name' => ''])['name'];
        }

        return $formFields;
    }
}
