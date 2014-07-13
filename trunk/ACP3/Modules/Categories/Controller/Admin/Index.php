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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Categories\Model
     */
    protected $categoriesModel;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Validate $validate,
        Core\Session $session,
        \Doctrine\DBAL\Connection $db,
        Categories\Model $categoriesModel)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules, $validate, $session);

        $this->db = $db;
        $this->categoriesModel = $categoriesModel;
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
                $config = new Core\Config($this->db, 'categories');
                $settings = $config->getSettings();

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

                $cache = new Categories\Cache($this->categoriesModel);
                $cache->setCache($_POST['module']);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
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

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/categories/index/delete', 'acp/categories');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            $isInUse = false;

            foreach ($items as $item) {
                if (!empty($item) && $this->categoriesModel->resultExists($item) === true) {
                    $category = $this->categoriesModel->getCategoryDeleteInfosById($item);

                    $className = "\\ACP3\\Modules\\" . ucfirst($category['module']) . "\\Model";
                    if (class_exists($className) === true) {
                        /** @var \ACP3\Core\Model $model */
                        $model = new $className($this->db);
                        if ($model->countAll('', $item) > 0) {
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
        $category = $this->categoriesModel->getOneById($this->uri->id);

        if (empty($category) === false) {
            if (empty($_POST) === false) {
                try {
                    $file = array();
                    if (!empty($_FILES['picture']['name'])) {
                        $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                        $file['name'] = $_FILES['picture']['name'];
                        $file['size'] = $_FILES['picture']['size'];
                    }
                    $config = new Core\Config($this->db, 'categories');
                    $settings = $config->getSettings();

                    $validator = $this->get('categories.validator');
                    $validator->validate($_POST, $file, $settings, $this->uri->id);

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

                    $bool = $this->categoriesModel->update($updateValues, $this->uri->id);

                    $cache = new Categories\Cache($this->categoriesModel);
                    $cache->setCache($this->categoriesModel->getModuleNameFromCategoryId($this->uri->id));

                    $this->session->unsetFormToken();

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            $this->view->assign('form', array_merge($category, $_POST));

            $this->session->generateFormToken();
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
        $config = new Core\Config($this->db, 'categories');

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

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->session->generateFormToken();
    }

}
