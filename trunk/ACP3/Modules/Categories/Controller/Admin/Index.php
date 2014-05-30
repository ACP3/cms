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

        $this->model = new Categories\Model($this->db, $this->lang);
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
                $settings = Core\Config::getSettings('categories');

                $this->model->validate($_POST, $file, $settings);

                $file_sql = null;
                if (!empty($file)) {
                    $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'categories');
                    $file_sql = array('picture' => $result['name']);
                }

                $moduleInfo = Core\Modules::getModuleInfo($_POST['module']);
                $insert_values = array(
                    'id' => '',
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'description' => Core\Functions::strEncode($_POST['description']),
                    'module_id' => $moduleInfo['id'],
                );
                if (is_array($file_sql) === true) {
                    $insert_values = array_merge($insert_values, $file_sql);
                }

                $bool = $this->model->insert($insert_values);
                $this->model->setCache($_POST['module']);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', array_merge(array('title' => '', 'description' => ''), $_POST));

        $mod_list = Core\Modules::getActiveModules();
        foreach ($mod_list as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $mod_list[$name]['selected'] = Core\Functions::selectEntry('module', $info['dir']);
            } else {
                unset($mod_list[$name]);
            }
        }
        $this->view->assign('mod_list', $mod_list);

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
                    Core\Functions::removeUploadedFile('categories', $category['picture']);
                    $bool = $this->model->delete($item);
                }
            }

            Core\Cache::purge('sql', 'categories');

            if ($isInUse === true) {
                $text = $this->lang->t('categories', 'category_is_in_use');
                $bool = false;
            } else {
                $text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
            }
            Core\Functions::setRedirectMessage($bool, $text, 'acp/categories');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/index/404');
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
                    $settings = Core\Config::getSettings('categories');

                    $this->model->validate($_POST, $file, $settings, $this->uri->id);

                    $updateValues = array(
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'description' => Core\Functions::strEncode($_POST['description']),
                    );

                    if (empty($file) === false) {
                        Core\Functions::removeUploadedFile('categories', $category['picture']);
                        $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'categories');
                        $updateValues['picture'] = $result['name'];
                    }

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    $this->model->setCache($this->model->getModuleNameFromCategoryId($this->uri->id));

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', array_merge($category, $_POST));

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/index/404');
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
        if (empty($_POST) === false) {
            try {
                $this->model->validateSettings($_POST, $this->lang);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'filesize' => (int)$_POST['filesize'],
                );
                $bool = Core\Config::setSettings('categories', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/categories');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('categories');

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->session->generateFormToken();
    }

}
