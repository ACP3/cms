<?php

namespace ACP3\Modules\Categories\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Categories;

/**
 * Description of CategoriesAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Categories\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Categories\Model($this->db);
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

                $validator = new Categories\Validator($this->lang, $this->model);
                $validator->validate($_POST, $file, $settings);

                $moduleInfo = Core\Modules::getModuleInfo($_POST['module']);
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

                $bool = $this->model->insert($insertValues);

                $cache = new Categories\Cache($this->model);
                $cache->setCache($_POST['module']);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge(array('title' => '', 'description' => ''), $_POST));

        $modules = Core\Modules::getActiveModules();
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
                if (!empty($item) && $this->model->resultExists($item) === true) {
                    $category = $this->model->getCategoryDeleteInfosById($item);

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
                    $bool = $this->model->delete($item);
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
            Core\Functions::setRedirectMessage($bool, $text, 'acp/categories');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $category = $this->model->getOneById($this->uri->id);

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

                    $validator = new Categories\Validator($this->lang, $this->model);
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

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    $cache = new Categories\Cache($this->model);
                    $cache->setCache($this->model->getModuleNameFromCategoryId($this->uri->id));

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
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
        Core\Functions::getRedirectMessage();

        $categories = $this->model->getAllWithModuleName();
        $c_categories = count($categories);

        if ($c_categories > 0) {
            $canDelete = Core\Modules::hasPermission('admin/categories/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent(Core\Functions::dataTable($config));
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
                $validator = new Categories\Validator($this->lang, $this->model);
                $validator->validateSettings($_POST, $this->lang);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'filesize' => (int)$_POST['filesize'],
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
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
