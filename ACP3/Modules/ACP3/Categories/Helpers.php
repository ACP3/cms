<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Categories
 */
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
     * @param \ACP3\Core\ACL $acl
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\Modules $modules
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Modules\ACP3\Categories\Cache $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\ACL $acl,
        Core\I18n\Translator $translator,
        Core\Modules $modules,
        Core\Http\RequestInterface $request,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\Secure $secureHelper,
        Cache $categoriesCache,
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
    }

    /**
     * Erzeugt eine neue Kategorie und gibt ihre ID zurück
     *
     * @param string $title
     * @param string $module
     *
     * @return integer
     */
    public function categoriesCreate($title, $module)
    {
        $moduleInfo = $this->modules->getModuleInfo($module);
        if ($this->categoryRepository->resultIsDuplicate($title, $moduleInfo['id'], '') === false) {
            $insertValues = [
                'id' => '',
                'title' => $this->secureHelper->strEncode($title),
                'picture' => '',
                'description' => '',
                'module_id' => $moduleInfo['id'],
            ];
            $result = $this->categoryRepository->insert($insertValues);

            $this->categoriesCache->saveCache($module);

            return $result;
        }

        return $this->categoryRepository->getOneByTitleAndModule($title, $module)['id'];
    }

    /**
     * Listet alle Kategorien eines Moduls auf
     *
     * @param string $module
     * @param string $categoryId
     * @param boolean $categoryCreate
     * @param string $formFieldName
     * @param string $customText
     *
     * @return array
     */
    public function categoriesList(
        $module,
        $categoryId = '',
        $categoryCreate = false,
        $formFieldName = 'cat',
        $customText = ''
    ) {
        $categories = [];

        $categories['custom_text'] = !empty($customText) ? $customText : $this->translator->t('system', 'pls_select');
        $categories['name'] = $formFieldName;

        $categories['categories'] = $this->categoriesCache->getCache($module);
        $cData = count($categories['categories']);
        for ($i = 0; $i < $cData; ++$i) {
            $categories['categories'][$i]['selected'] = $this->formsHelper->selectEntry(
                $formFieldName,
                $categories['categories'][$i]['id'],
                $categoryId
            );
        }

        if ($categoryCreate === true && $this->acl->hasPermission('admin/categories/index/create') === true) {
            $categories['create']['name'] = $formFieldName . '_create';
            $categories['create']['value'] = $this->request->getPost()->get('create', ['name' => ''])['name'];
        }

        return $categories;
    }
}
