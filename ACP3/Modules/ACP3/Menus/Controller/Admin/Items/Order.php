<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Order
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Order extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var Core\NestedSet\Operation\Sort
     */
    protected $sortOperation;

    /**
     * Order constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Core\NestedSet\Operation\Sort $sortOperation
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\NestedSet\Operation\Sort $sortOperation,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->sortOperation = $sortOperation;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action)
    {
        if ($this->menuItemRepository->menuItemExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->menusCache->saveMenusCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirect()->temporary('acp/menus');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
