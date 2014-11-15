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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Menus\Model
     */
    protected $menusModel;
    /**
     * @var Menus\Cache
     */
    protected $menusCache;

    /**
     * @param Core\Context\Admin $context
     * @param \Doctrine\DBAL\Connection $db
     * @param Core\Helpers\Secure $secureHelper
     * @param Menus\Model $menusModel
     * @param Menus\Cache $menusCache
     */
    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        Menus\Model $menusModel,
        Menus\Cache $menusCache)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $this->view->assign('form', array_merge(array('index_name' => '', 'title' => ''), $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/menus/index/delete', 'acp/menus');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            $cache = new Core\Cache('menus');

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

            $this->menusCache->setMenuItemsCache();

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
                $this->_editPost($_POST);
            }

            $this->view->assign('form', array_merge($menu, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $menus = $this->menusModel->getAllMenus();
        $c_menus = count($menus);

        if ($c_menus > 0) {
            $canDeleteItem = $this->acl->hasPermission('admin/menus/items/delete');
            $canSortItem = $this->acl->hasPermission('admin/menus/items/order');
            $this->view->assign('can_delete_item', $canDeleteItem);
            $this->view->assign('can_order_item', $canSortItem);
            $this->view->assign('can_delete', $this->acl->hasPermission('admin/menus/index/delete'));
            $this->view->assign('can_edit', $this->acl->hasPermission('admin/menus/index/edit'));
            $this->view->assign('colspan', $canDeleteItem && $canSortItem ? 5 : ($canDeleteItem || $canSortItem ? 4 : 3));

            $pagesList = $this->get('menus.helpers')->menuItemsList();
            for ($i = 0; $i < $c_menus; ++$i) {
                if (isset($pagesList[$menus[$i]['index_name']]) === false) {
                    $pagesList[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
                    $pagesList[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
                    $pagesList[$menus[$i]['index_name']]['items'] = [];
                }
            }
            $this->view->assign('pages_list', $pagesList);
        }
    }

    /**
     * @param array $formData
     */
    private function _createPost(array $formData)
    {
        try {
            $validator = $this->get('menus.validator');
            $validator->validateCreate($formData);

            $insertValues = array(
                'id' => '',
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            );

            $lastId = $this->menusModel->insert($insertValues);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/menus');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _editPost(array $formData)
    {
        try {
            $validator = $this->get('menus.validator');
            $validator->validateEdit($formData);

            $updateValues = array(
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            );

            $bool = $this->menusModel->update($updateValues, $this->request->id);

            $this->menusCache->setMenuItemsCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}