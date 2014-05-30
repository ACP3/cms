<?php

namespace ACP3\Modules\Menus\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Menus;

/**
 * Description of MenusAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     *
     * @var Menus\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Menus\Model($this->db, $this->lang, $this->uri);
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateCreate($_POST);

                $insertValues = array(
                    'id' => '',
                    'index_name' => $_POST['index_name'],
                    'title' => Core\Functions::strEncode($_POST['title']),
                );

                $lastId = $this->model->insert($insertValues);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/menus');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/menus');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', array_merge(array('index_name' => '', 'title' => ''), $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/menus/index/delete', 'acp/menus');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            foreach ($items as $item) {
                if (!empty($item) && $this->model->menuExists($item) === true) {
                    // Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
                    $items = $this->model->getAllItemsByBlockId($item);
                    foreach ($items as $row) {
                        $nestedSet->deleteNode($row['id']);
                    }

                    $block = $this->model->getMenuNameById($item);
                    $bool = $this->model->delete($item);
                    Core\Cache::delete('visible_items_' . $block, 'menus');
                }
            }

            $this->model->setMenuItemsCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionEdit()
    {
        $menu = $this->model->getOneById($this->uri->id);

        if (empty($menu) === false) {
            if (empty($_POST) === false) {
                try {
                    $this->model->validateEdit($_POST);

                    $updateValues = array(
                        'index_name' => $_POST['index_name'],
                        'title' => Core\Functions::strEncode($_POST['title']),
                    );

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    $this->model->setMenuItemsCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/menus');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', array_merge($menu, $_POST));

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $menus = $this->model->getAllMenus();
        $c_menus = count($menus);

        if ($c_menus > 0) {
            $canDeleteItem = Core\Modules::hasPermission('admin/menus/items/delete');
            $canSortItem = Core\Modules::hasPermission('admin/menus/items/order');
            $this->view->assign('can_delete_item', $canDeleteItem);
            $this->view->assign('can_order_item', $canSortItem);
            $this->view->assign('can_delete', Core\Modules::hasPermission('admin/menus/index/delete'));
            $this->view->assign('can_edit', Core\Modules::hasPermission('admin/menus/index/edit'));
            $this->view->assign('colspan', $canDeleteItem && $canSortItem ? 5 : ($canDeleteItem || $canSortItem ? 4 : 3));

            $pagesList = Menus\Helpers::menuItemsList();
            for ($i = 0; $i < $c_menus; ++$i) {
                if (isset($pagesList[$menus[$i]['index_name']]) === false) {
                    $pagesList[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
                    $pagesList[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
                    $pagesList[$menus[$i]['index_name']]['items'] = array();
                }
            }
            $this->view->assign('pages_list', $pagesList);
        }
    }

}