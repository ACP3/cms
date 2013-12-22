<?php

namespace ACP3\Modules\Categories\Controller;

use ACP3\Core;
use ACP3\Modules\Categories;

/**
 * Description of CategoriesAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);

        $this->model = new Categories\Model($this->db);
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $file = array();
                if (!empty($_FILES['picture']['name'])) {
                    $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                    $file['name'] = $_FILES['picture']['name'];
                    $file['size'] = $_FILES['picture']['size'];
                }
                $settings = Core\Config::getSettings('categories');

                $this->model->validate($_POST, $file, $settings, $this->lang);

                $file_sql = null;
                if (!empty($file)) {
                    $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'categories');
                    $file_sql = array('picture' => $result['name']);
                }

                $mod_id = $this->db->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['module']));
                $insert_values = array(
                    'id' => '',
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'description' => Core\Functions::strEncode($_POST['description']),
                    'module_id' => $mod_id,
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

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'description' => ''));

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
        $items = $this->_deleteItem('acp/categories/delete', 'acp/categories');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            $in_use = false;

            foreach ($items as $item) {
                if (!empty($item) && $this->model->resultExists($item) === true) {
                    $category = $this->db->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($item));
                    if ($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . $category['module'] . ' WHERE category_id = ?', array($item)) > 0) {
                        $in_use = true;
                    } else {
                        // Kategoriebild ebenfalls löschen
                        Core\Functions::removeUploadedFile('categories', $category['picture']);
                        $bool = $this->model->delete($item);
                    }
                }
            }

            Core\Cache::purge('sql', 'categories');

            if ($in_use === true) {
                $text = $this->lang->t('categories', 'category_is_in_use');
                $bool = false;
            } else {
                $text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
            }
            Core\Functions::setRedirectMessage($bool, $text, 'acp/categories');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $category = $this->model->getOneById($this->uri->id);

        if (empty($category) === false) {
            if (isset($_POST['submit']) === true) {
                try {
                    $file = array();
                    if (!empty($_FILES['picture']['name'])) {
                        $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                        $file['name'] = $_FILES['picture']['name'];
                        $file['size'] = $_FILES['picture']['size'];
                    }
                    $settings = Core\Config::getSettings('categories');

                    $this->model->validate($_POST, $file, $settings, $this->lang, $this->uri->id);

                    $update_values = array(
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'description' => Core\Functions::strEncode($_POST['description']),
                    );

                    if (empty($file) === false) {
                        Core\Functions::removeUploadedFile('categories', $category['picture']);
                        $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'categories');
                        $update_values['picture'] = $result['name'];
                    }

                    $bool = $this->model->update($update_values, $this->uri->id);

                    $this->model->setCache($this->model->getModuleNameFromCategoryId($this->uri->id));

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $category);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $categories = $this->model->getAllWithModuleName();
        $c_categories = count($categories);

        if ($c_categories > 0) {
            $can_delete = Core\Modules::hasPermission('categories', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::datatable($config));
            for ($i = 0; $i < $c_categories; ++$i) {
                $categories[$i]['module'] = $this->lang->t($categories[$i]['module'], $categories[$i]['module']);
            }
            $this->view->assign('categories', $categories);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionSettings()
    {
        if (isset($_POST['submit']) === true) {
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

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

        $this->session->generateFormToken();
    }

}
