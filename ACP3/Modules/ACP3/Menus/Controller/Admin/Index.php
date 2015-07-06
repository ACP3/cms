<?php

namespace ACP3\Modules\ACP3\Menus\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers
     */
    protected $menusHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validator
     */
    protected $menusValidator;

    /**
     * @param \ACP3\Core\Context\Admin      $context
     * @param \ACP3\Core\DB                 $db
     * @param \ACP3\Core\Helpers\Secure     $secureHelper
     * @param \ACP3\Modules\ACP3\Menus\Helpers   $menusHelpers
     * @param \ACP3\Modules\ACP3\Menus\Model     $menusModel
     * @param \ACP3\Modules\ACP3\Menus\Cache     $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Validator $menusValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\DB $db,
        Core\Helpers\Secure $secureHelper,
        Menus\Helpers $menusHelpers,
        Menus\Model $menusModel,
        Menus\Cache $menusCache,
        Menus\Validator $menusValidator)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->menusHelpers = $menusHelpers;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
        $this->menusValidator = $menusValidator;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $this->view->assign('form', array_merge(['index_name' => '', 'title' => ''], $_POST));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
            $bool = false;
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);

            foreach ($items as $item) {
                if (!empty($item) && $this->menusModel->menuExists($item) === true) {
                    // Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
                    $items = $this->menusModel->getAllItemsByBlockId($item);
                    foreach ($items as $row) {
                        $nestedSet->deleteNode($row['id']);
                    }

                    $block = $this->menusModel->getMenuNameById($item);
                    $bool = $this->menusModel->delete($item);
                    $this->menusCache->getCacheDriver()->delete(Menus\Cache::CACHE_ID_VISIBLE . $block);
                }
            }

            $this->menusCache->setMenuItemsCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $menu = $this->menusModel->getOneById($this->request->id);

        if (empty($menu) === false) {
            $this->breadcrumb->setTitlePostfix($menu['title']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST);
            }

            $this->view->assign('form', array_merge($menu, $_POST));

            $this->secureHelper->generateFormToken($this->request->getQuery());
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

            $pagesList = $this->menusHelpers->menuItemsList();
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
    protected function _createPost(array $formData)
    {
        try {
            $this->menusValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $lastId = $this->menusModel->insert($insertValues);

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    protected function _editPost(array $formData)
    {
        try {
            $this->menusValidator->validate($formData, (int)$this->request->id);

            $updateValues = [
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->menusModel->update($updateValues, $this->request->id);

            $this->menusCache->setMenuItemsCache();

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
