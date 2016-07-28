<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\NestedSet\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext        $context
     * @param \ACP3\Core\NestedSet\NestedSet                              $nestedSet
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                    $menusCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\NestedSet\NestedSet $nestedSet,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action, function ($items) {
            $bool = false;

            foreach ($items as $item) {
                // URI-Alias löschen
                $itemUri = $this->menuItemRepository->getMenuItemUriById($item);
                $bool = $this->nestedSet->deleteNode($item, Menus\Model\Repository\MenuItemRepository::TABLE_NAME,
                    true);

                if ($this->uriAliasManager) {
                    $this->uriAliasManager->deleteUriAlias($itemUri);
                }
            }

            $this->menusCache->saveMenusCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        }, null, 'acp/menus'
        );
    }
}
