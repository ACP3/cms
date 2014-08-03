<?php

namespace ACP3\Modules\Categories\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Categories;

/**
 * Class Index
 * @package ACP3\Modules\Categories\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Categories\Model
     */
    protected $categoriesModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $categoriesConfig;
    /**
     * @var \ACP3\Modules\Categories\Cache
     */
    protected $categoriesCache;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;

    public function __construct(
        Core\Context\Admin $context,
        Categories\Model $categoriesModel,
        Core\Config $categoriesConfig,
        Categories\Cache $categoriesCache,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->categoriesModel = $categoriesModel;
        $this->categoriesConfig = $categoriesConfig;
        $this->categoriesCache = $categoriesCache;
        $this->secureHelper = $secureHelper;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $file = array();
                if (!empty($_FILES['picture']['name'])) {
                    $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                    $file['name'] = $_FILES['picture']['name'];
                    $file['size'] = $_FILES['picture']['size'];
                }

                $settings = $this->categoriesConfig->getSettings();

                $validator = $this->get('categories.validator');
                $validator->validate($_POST, $file, $settings);

                $moduleInfo = $this->modules->getModuleInfo($_POST['module']);
                $insertValues = array(
                    'id' => '',
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'description' => Core\Functions::strEncode($_POST['description']),
                    'module_id' => $moduleInfo['id'],
                );
                if (!empty($file)) {
                    $upload = new Core\Helpers\Upload('categories');
                    $result = $upload->moveFile($file['tmp_name'], $file['name']);
                    $insertValues['picture'] = $result['name'];
                }

                $bool = $this->categoriesModel->insert($insertValues);

                $this->categoriesCache->setCache($_POST['module']);

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge(array('title' => '', 'description' => ''), $_POST));

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = Core\Functions::selectEntry('module', $info['dir']);
            } else {
                unset($modules[$name]);
            }
        }
        $this->view->assign('mod_list', $modules);

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/categories/index/delete', 'acp/categories');

        if ($this->request->action === 'confirmed') {
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

            $cache = new Core\Cache2('categories');
            $cache->getDriver()->deleteAll();

            if ($isInUse === true) {
                $text = $this->lang->t('categories', 'category_is_in_use');
                $bool = false;
            } else {
                $text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
            }

            $this->redirectMessages()->setMessage($bool, $text, 'acp/categories');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $category = $this->categoriesModel->getOneById($this->request->id);

        if (empty($category) === false) {
            if (empty($_POST) === false) {
                try {
                    $file = array();
                    if (!empty($_FILES['picture']['name'])) {
                        $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                        $file['name'] = $_FILES['picture']['name'];
                        $file['size'] = $_FILES['picture']['size'];
                    }
                    $settings = $this->categoriesConfig->getSettings();

                    $validator = $this->get('categories.validator');
                    $validator->validate($_POST, $file, $settings, $this->request->id);

                    $updateValues = array(
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'description' => Core\Functions::strEncode($_POST['description']),
                    );

                    if (empty($file) === false) {
                        $upload = new Core\Helpers\Upload('categories');
                        $upload->removeUploadedFile($category['picture']);
                        $result = $upload->moveFile($file['tmp_name'], $file['name']);
                        $updateValues['picture'] = $result['name'];
                    }

                    $bool = $this->categoriesModel->update($updateValues, $this->request->id);

                    $this->categoriesCache->setCache($this->categoriesModel->getModuleNameFromCategoryId($this->request->id));

                    $this->secureHelper->unsetFormToken($this->request->query);

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
                }
            }

            $this->view->assign('form', array_merge($category, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $categories = $this->categoriesModel->getAllWithModuleName();
        $c_categories = count($categories);

        if ($c_categories > 0) {
            $canDelete = $this->modules->hasPermission('admin/categories/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));
            for ($i = 0; $i < $c_categories; ++$i) {
                $categories[$i]['module'] = $this->lang->t($categories[$i]['module'], $categories[$i]['module']);
            }
            $this->view->assign('categories', $categories);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        $config = $this->categoriesConfig;

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('categories.validator');
                $validator->validateSettings($_POST, $this->lang);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'filesize' => (int)$_POST['filesize'],
                );
                $bool = $config->setSettings($data);

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

}