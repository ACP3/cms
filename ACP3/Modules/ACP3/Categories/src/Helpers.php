<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\CategoriesModel;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

class Helpers
{
    /**
     * @var Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var Cache
     */
    protected $categoriesCache;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoriesModel
     */
    private $categoriesModel;

    /**
     * @param \ACP3\Core\ACL                      $acl
     * @param \ACP3\Core\I18n\Translator          $translator
     * @param \ACP3\Core\Modules                  $modules
     * @param \ACP3\Core\Http\RequestInterface    $request
     * @param \ACP3\Core\Helpers\Forms            $formsHelper
     * @param \ACP3\Core\Helpers\Secure           $secureHelper
     * @param \ACP3\Modules\ACP3\Categories\Cache $categoriesCache
     */
    public function __construct(
        Core\ACL $acl,
        Core\I18n\Translator $translator,
        Core\Modules $modules,
        Core\Http\RequestInterface $request,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\Secure $secureHelper,
        Cache $categoriesCache,
        CategoriesModel $categoriesModel,
        CategoryRepository $categoryRepository
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
        $this->modules = $modules;
        $this->request = $request;
        $this->formsHelper = $formsHelper;
        $this->secureHelper = $secureHelper;
        $this->categoriesCache = $categoriesCache;
        $this->categoryRepository = $categoryRepository;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * Erzeugt eine neue Kategorie und gibt ihre ID zurück.
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function categoriesCreate(string $categoryTitle, string $moduleName)
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
     * @return array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function categoriesList(
        string $moduleName,
        ?int $categoryId = null,
        bool $categoryCreate = false,
        string $formFieldName = 'cat',
        ?string $customText = null
    ) {
        $categories = $this->categoriesCache->getCache($moduleName);
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
