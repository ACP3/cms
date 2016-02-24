<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Delete extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext        $context
     * @param \ACP3\Core\NestedSet                              $nestedSet
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                    $menusCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\NestedSet $nestedSet,
        Menus\Model\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    // URI-Alias löschen
                    $itemUri = $this->menuItemRepository->getMenuItemUriById($item);
                    $bool = $this->nestedSet->deleteNode($item, Menus\Model\MenuItemRepository::TABLE_NAME, true);
                    $this->seo->deleteUriAlias($itemUri);
                }

                $this->menusCache->saveMenusCache();

                return $bool;
            },
            null,
            'acp/menus'
        );
    }
}
