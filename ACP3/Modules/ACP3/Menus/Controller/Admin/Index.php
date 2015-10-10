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
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList
     */
    protected $menusHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validator\Menu
     */
    protected $menusValidator;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext        $context
     * @param \ACP3\Core\NestedSet                              $nestedSet
     * @param \ACP3\Core\Helpers\FormToken                      $formTokenHelper
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList    $menusHelpers
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuRepository     $menuRepository
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                    $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Validator\Menu           $menusValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\NestedSet $nestedSet,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Helpers\MenuItemsList $menusHelpers,
        Menus\Model\MenuRepository $menuRepository,
        Menus\Model\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache,
        Menus\Validator\Menu $menusValidator)
    {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->menusHelpers = $menusHelpers;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->menusValidator = $menusValidator;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge(['index_name' => '', 'title' => ''], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    public function actionDelete($action = '')
    {
        $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function($items) {
                $bool = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->menuRepository->menuExists($item) === true) {
                        // Delete the assigned menu items and update the nested set tree
                        $items = $this->menuItemRepository->getAllItemsByBlockId($item);
                        foreach ($items as $row) {
                            $this->nestedSet->deleteNode(
                                $row['id'],
                                Menus\Model\MenuItemRepository::TABLE_NAME,
                                true
                            );
                        }

                        $block = $this->menuRepository->getMenuNameById($item);
                        $bool = $this->menuRepository->delete($item);
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
        $menu = $this->menuRepository->getOneById($id);

        if (empty($menu) === false) {
            $this->breadcrumb->setTitlePostfix($menu['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            $this->view->assign('form', array_merge($menu, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $menus = $this->menuRepository->getAllMenus();
        $c_menus = count($menus);

        if ($c_menus > 0) {
            $canDeleteItem = $this->acl->hasPermission('admin/menus/items/delete');
            $canSortItem = $this->acl->hasPermission('admin/menus/items/order');
            $this->view->assign('can_delete_item', $canDeleteItem);
            $this->view->assign('can_order_item', $canSortItem);
            $this->view->assign('can_delete', $this->acl->hasPermission('admin/menus/index/delete'));
            $this->view->assign('can_edit', $this->acl->hasPermission('admin/menus/index/edit'));
            $this->view->assign('colspan', $canDeleteItem && $canSortItem ? 5 : ($canDeleteItem || $canSortItem ? 4 : 3));

            $menuItems = $this->menusHelpers->menuItemsList();
            for ($i = 0; $i < $c_menus; ++$i) {
                if (isset($menuItems[$menus[$i]['index_name']]) === false) {
                    $menuItems[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
                    $menuItems[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
                    $menuItems[$menus[$i]['index_name']]['items'] = [];
                }
            }
            $this->view->assign('pages_list', $menuItems);
        }
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->actionHelper->handleCreatePostAction(function() use ($formData) {
            $this->menusValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $lastId = $this->menuRepository->insert($insertValues);

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function _editPost(array $formData, $id)
    {
        $this->actionHelper->handleEditPostAction(function() use ($formData, $id) {
            $this->menusValidator->validate($formData, $id);

            $updateValues = [
                'index_name' => $formData['index_name'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->menuRepository->update($updateValues, $id);

            $this->menusCache->saveMenusCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
