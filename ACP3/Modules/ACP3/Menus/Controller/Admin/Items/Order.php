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
 * Class Order
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Order extends Core\Controller\AdminAction
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
     * Order constructor.
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
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action)
    {
        if ($this->menuItemRepository->menuItemExists($id) === true) {
            $this->nestedSet->sort(
                $id,
                $action,
                Menus\Model\MenuItemRepository::TABLE_NAME,
                true
            );

            $this->menusCache->saveMenusCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirect()->temporary('acp/menus');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
