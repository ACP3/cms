<?php

namespace ACP3\Modules\ACP3\Menus\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
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
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\NestedSet                       $nestedSet
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Menus\Helpers           $menusHelpers
     * @param \ACP3\Modules\ACP3\Menus\Model             $menusModel
     * @param \ACP3\Modules\ACP3\Menus\Cache             $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Validator         $menusValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\NestedSet $nestedSet,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Helpers $menusHelpers,
        Menus\Model $menusModel,
        Menus\Cache $menusCache,
        Menus\Validator $menusValidator)
    {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->menusHelpers = $menusHelpers;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
        $this->menusValidator = $menusValidator;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge(['index_name' => '', 'title' => ''], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    public function actionDelete($action = '')
    {
        $this->handleDeleteAction(
            $action,
            function($items) {
                $bool = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->menusModel->menuExists($item) === true) {
                        // Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
                        $items = $this->menusModel->getAllItemsByBlockId($item);
                        foreach ($items as $row) {
                            $this->nestedSet->deleteNode(
                                $row['id'],
                                Menus\Model::TABLE_NAME_ITEMS,
                                true
                            );
                        }

                        $block = $this->menusModel->getMenuNameById($item);
                        $bool = $this->menusModel->delete($item);
                        $this->menusCache->getCacheDriver()->delete(Menus\Cache::CACHE_ID_VISIBLE . $block);
                    }
                }

                $this->menusCache->saveMenusCache();

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $menu = $this->menusModel->getOneById($id);

        if (empty($menu) === false) {
            $this->breadcrumb->setTitlePostfix($menu['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            $this->view->assign('form', array_merge($menu, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
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
        $this->handleCreatePostAction(function() use ($formData) {
            $this->menusValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $lastId = $this->menusModel->insert($insertValues);

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function _editPost(array $formData, $id)
    {
        $this->handleEditPostAction(function() use ($formData, $id) {
            $this->menusValidator->validate($formData, $id);

            $updateValues = [
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->menusModel->update($updateValues, $id);

            $this->menusCache->saveMenusCache();

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $bool;
        });
    }
}
