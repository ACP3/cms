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
     * @var Core\View
     */
    protected $view;
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
     * @param \ACP3\Core\ACL                                         $acl
     * @param \ACP3\Core\I18n\Translator                             $translator
     * @param \ACP3\Core\Modules                                     $modules
     * @param \ACP3\Core\Http\RequestInterface                       $request
     * @param \ACP3\Core\View                                        $view
     * @param \ACP3\Core\Helpers\Forms                               $formsHelper
     * @param \ACP3\Core\Helpers\Secure                              $secureHelper
     * @param \ACP3\Modules\ACP3\Categories\Cache                    $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\ACL $acl,
        Core\I18n\Translator $translator,
        Core\Modules $modules,
        Core\Http\RequestInterface $request,
        Core\View $view,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\Secure $secureHelper,
        Cache $categoriesCache,
        CategoryRepository $categoryRepository
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
        $this->modules = $modules;
        $this->request = $request;
        $this->view = $view;
        $this->formsHelper = $formsHelper;
        $this->secureHelper = $secureHelper;
        $this->categoriesCache = $categoriesCache;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Überprüft, ob eine Kategorie existiert
     *
     * @param integer $categoryId
     *
     * @return boolean
     */
    public function categoryExists($categoryId)
    {
        return $this->categoryRepository->resultExists($categoryId);
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
        if ($this->categoryIsDuplicate($title, $moduleInfo['id']) === false) {
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
        } else {
            return $this->categoryRepository->getOneByTitleAndModule($title, $module)['id'];
        }
    }

    /**
     * Überprüft, ob bereits eine Kategorie mit dem selben Namen existiert
     *
     * @param string     $title
     * @param int        $moduleId
     * @param int|string $categoryId
     *
     * @return boolean
     */
    public function categoryIsDuplicate($title, $moduleId, $categoryId = '')
    {
        return $this->categoryRepository->resultIsDuplicate($title, $moduleId, $categoryId);
    }

    /**
     * Listet alle Kategorien eines Moduls auf
     *
     * @param string  $module
     * @param string  $categoryId
     * @param boolean $categoryCreate
     * @param string  $formFieldName
     * @param string  $customText
     *
     * @return string
     */
    public function categoriesList($module, $categoryId = '', $categoryCreate = false, $formFieldName = 'cat', $customText = '')
    {
        $categories = [];
        $data = $this->categoriesCache->getCache($module);
        $cData = count($data);

        $categories['custom_text'] = !empty($customText) ? $customText : $this->translator->t('system', 'pls_select');
        $categories['name'] = $formFieldName;
        if ($cData > 0) {
            for ($i = 0; $i < $cData; ++$i) {
                $data[$i]['selected'] = $this->formsHelper->selectEntry('cat', $data[$i]['id'], $categoryId);
            }
            $categories['categories'] = $data;
        } else {
            $categories['categories'] = [];
        }
        if ($categoryCreate === true && $this->acl->hasPermission('admin/categories/index/create') === true) {
            $categories['create']['name'] = $formFieldName . '_create';
            $categories['create']['value'] = $this->request->getPost()->get('create', ['name' => ''])['name'];
        }
        $this->view->assign('categories', $categories);
        return $this->view->fetchTemplate('categories/create_list.tpl');
    }
}
