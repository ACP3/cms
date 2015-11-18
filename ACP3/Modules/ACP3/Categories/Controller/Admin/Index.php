<?php

namespace ACP3\Modules\ACP3\Categories\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Categories\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Cache
     */
    protected $categoriesCache;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Validator
     */
    protected $categoriesValidator;
    /**
     * @var Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext             $context
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     * @param \ACP3\Modules\ACP3\Categories\Cache                    $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Validator                $categoriesValidator
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Categories\Model\CategoryRepository $categoryRepository,
        Categories\Cache $categoriesCache,
        Categories\Validator $categoriesValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->categoryRepository = $categoryRepository;
        $this->categoriesCache = $categoriesCache;
        $this->categoriesValidator = $categoriesValidator;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all());
        }

        $this->view->assign('form', array_merge(['title' => '', 'description' => ''], $this->request->getPost()->all()));

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = $this->get('core.helpers.forms')->selectEntry('module', $info['dir']);
            } else {
                unset($modules[$name]);
            }
        }
        $this->view->assign('mod_list', $modules);

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $file = $this->request->getFiles()->get('picture');

            $this->categoriesValidator->validate($formData, $file, $this->config->getSettings('categories'));

            $moduleInfo = $this->modules->getModuleInfo($formData['module']);
            $insertValues = [
                'id' => '',
                'title' => Core\Functions::strEncode($formData['title']),
                'description' => Core\Functions::strEncode($formData['description']),
                'module_id' => $moduleInfo['id'],
            ];
            if (!empty($file)) {
                $upload = new Core\Helpers\Upload('categories');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $insertValues['picture'] = $result['name'];
            }

            $bool = $this->categoryRepository->insert($insertValues);

            $this->categoriesCache->saveCache(strtolower($formData['module']));

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                $isInUse = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->categoryRepository->resultExists($item) === true) {
                        $category = $this->categoryRepository->getCategoryDeleteInfosById($item);

                        $serviceId = strtolower($category['module'] . '.model');
                        if ($this->container->has($serviceId)) {
                            if ($this->get($serviceId)->countAll('', $item) > 0) {
                                $isInUse = true;
                                continue;
                            }
                        }

                        // Kategoriebild ebenfalls löschen
                        $upload = new Core\Helpers\Upload('categories');
                        $upload->removeUploadedFile($category['picture']);
                        $bool = $this->categoryRepository->delete($item);
                    }
                }

                $this->categoriesCache->getCacheDriver()->deleteAll();

                if ($isInUse === true) {
                    $text = $this->lang->t('categories', 'category_is_in_use');
                    $bool = false;
                } else {
                    $text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
                }

                $this->redirectMessages()->setMessage($bool, $text);
            }
        );
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $category = $this->categoryRepository->getOneById($id);

        if (empty($category) === false) {
            $this->breadcrumb->setTitlePostfix($category['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $category, $id);
            }

            $this->view->assign('form', array_merge($category, $this->request->getPost()->all()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @param array $category
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $category, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $category, $id) {
            $file = $this->request->getFiles()->get('picture');

            $this->categoriesValidator->validate($formData, $file, $this->config->getSettings('categories'), $id);

            $updateValues = [
                'title' => Core\Functions::strEncode($formData['title']),
                'description' => Core\Functions::strEncode($formData['description']),
            ];

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload('categories');
                $upload->removeUploadedFile($category['picture']);
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $updateValues['picture'] = $result['name'];
            }

            $bool = $this->categoryRepository->update($updateValues, $id);

            $this->categoriesCache->saveCache($this->categoryRepository->getModuleNameFromCategoryId($id));

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    public function actionIndex()
    {
        $categories = $this->categoryRepository->getAllWithModuleName();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($categories)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/categories/index/delete')
            ->setResourcePathEdit('admin/categories/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->lang->t('categories', 'title'),
                'type' => 'text',
                'fields' => ['title'],
                'default_sort' => true
            ], 30)
            ->addColumn([
                'label' => $this->lang->t('system', 'description'),
                'type' => 'text',
                'fields' => ['description']
            ], 20)
            ->addColumn([
                'label' => $this->lang->t('categories', 'module'),
                'type' => 'translate',
                'fields' => ['module'],
            ], 20)
            ->addColumn([
                'label' => $this->lang->t('system', 'id'),
                'type' => 'integer',
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($categories) > 0
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('categories');

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->all()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->categoriesValidator->validateSettings($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'filesize' => (int)$formData['filesize'],
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'categories');
        });
    }
}
