<?php

namespace ACP3\Modules\Menus\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Menus;

/**
 * Class Index
 * @package ACP3\Modules\Menus\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Menus\Model
     */
    protected $menusModel;

    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        Menus\Model $menusModel)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->menusModel = $menusModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('menus.validator');
                $validator->validateCreate($_POST);

                $insertValues = array(
                    'id' => '',
                    'index_name' => $_POST['index_name'],
                    'title' => Core\Functions::strEncode($_POST['title']),
                );

                $lastId = $this->menusModel->insert($insertValues);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/menus');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge(array('index_name' => '', 'title' => ''), $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/menus/index/delete', 'acp/menus');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            $cache = new Core\Cache2('menus');

            foreach ($items as $item) {
                if (!empty($item) && $this->menusModel->menuExists($item) === true) {
                    // Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
                    $items = $this->menusModel->getAllItemsByBlockId($item);
                    foreach ($items as $row) {
                        $nestedSet->deleteNode($row['id']);
                    }

                    $block = $this->menusModel->getMenuNameById($item);
                    $bool = $this->menusModel->delete($item);
                    $cache->delete(Menus\Cache::CACHE_ID_VISIBLE . $block);
                }
            }

            $menusCache = new Menus\Cache($this->lang, $this->menusModel);
            $menusCache->setMenuItemsCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $menu = $this->menusModel->getOneById($this->request->id);

        if (empty($menu) === false) {
            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('menus.validator');
                    $validator->validateEdit($_POST);

                    $updateValues = array(
                        'index_name' => $_POST['index_name'],
                        'title' => Core\Functions::strEncode($_POST['title']),
                    );

                    $bool = $this->menusModel->update($updateValues, $this->request->id);

                    $cache = new Menus\Cache($this->lang, $this->menusModel);
                    $cache->setMenuItemsCache();

                    $this->session->unsetFormToken();

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
                }
            }

            $this->view->assign('form', array_merge($menu, $_POST));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $menus = $this->menusModel->getAllMenus();
        $c_menus = count($menus);

        if ($c_menus > 0) {
            $canDeleteItem = $this->modules->hasPermission('admin/menus/items/delete');
            $canSortItem = $this->modules->hasPermission('admin/menus/items/order');
            $this->view->assign('can_delete_item', $canDeleteItem);
            $this->view->assign('can_order_item', $canSortItem);
            $this->view->assign('can_delete', $this->modules->hasPermission('admin/menus/index/delete'));
            $this->view->assign('can_edit', $this->modules->hasPermission('admin/menus/index/edit'));
            $this->view->assign('colspan', $canDeleteItem && $canSortItem ? 5 : ($canDeleteItem || $canSortItem ? 4 : 3));

            $pagesList = $this->get('menus.helpers')->menuItemsList();
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