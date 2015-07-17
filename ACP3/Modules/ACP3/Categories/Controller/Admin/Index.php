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
     * @var Categories\Model
     */
    protected $categoriesModel;
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
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Modules\ACP3\Categories\Model        $categoriesModel
     * @param \ACP3\Modules\ACP3\Categories\Cache        $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Validator    $categoriesValidator
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Categories\Model $categoriesModel,
        Categories\Cache $categoriesCache,
        Categories\Validator $categoriesValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->categoriesModel = $categoriesModel;
        $this->categoriesCache = $categoriesCache;
        $this->categoriesValidator = $categoriesValidator;
        $this->formTokenHelper = $formTokenHelper;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge(['title' => '', 'description' => ''], $this->request->getPost()->getAll()));

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = $this->get('core.helpers.forms')->selectEntry('module', $info['dir']);
            } else {
                unset($modules[$name]);
            }
        }
        $this->view->assign('mod_list', $modules);

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        try {
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

            $bool = $this->categoriesModel->insert($insertValues);

            $this->categoriesCache->saveCache(strtolower($formData['module']));

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $items = $this->_deleteItem();

        if ($action === 'confirmed') {
            $bool = false;
            $isInUse = false;

            foreach ($items as $item) {
                if (!empty($item) && $this->categoriesModel->resultExists($item) === true) {
                    $category = $this->categoriesModel->getCategoryDeleteInfosById($item);

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
                    $bool = $this->categoriesModel->delete($item);
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
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $category = $this->categoriesModel->getOneById($id);

        if (empty($category) === false) {
            $this->breadcrumb->setTitlePostfix($category['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $category, $id);
            }

            $this->view->assign('form', array_merge($category, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @param array $category
     * @param int   $id
     */
    protected function _editPost(array $formData, array $category, $id)
    {
        try {
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

            $bool = $this->categoriesModel->update($updateValues, $id);

            $this->categoriesCache->saveCache($this->categoriesModel->getModuleNameFromCategoryId($id));

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionIndex()
    {
        $categories = $this->categoriesModel->getAllWithModuleName();
        $c_categories = count($categories);

        if ($c_categories > 0) {
            $canDelete = $this->acl->hasPermission('admin/categories/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            for ($i = 0; $i < $c_categories; ++$i) {
                $categories[$i]['module'] = $this->lang->t($categories[$i]['module'], $categories[$i]['module']);
            }
            $this->view->assign('categories', $categories);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('categories');

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        try {
            $this->categoriesValidator->validateSettings($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'filesize' => (int)$formData['filesize'],
            ];
            $bool = $this->config->setSettings($data, 'categories');

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
