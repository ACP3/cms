<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class Order extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \Toflar\Psr6HttpCacheStore\Psr6Store
     */
    private $httpCacheStore;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Core\NestedSet\Operation\Sort $sortOperation,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache,
        Psr6Store $httpCacheStore
    ) {
        parent::__construct($context);

        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->sortOperation = $sortOperation;
        $this->redirectResponse = $redirectResponse;
        $this->httpCacheStore = $httpCacheStore;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if ($this->menuItemRepository->menuItemExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->menusCache->saveMenusCache();

            $this->httpCacheStore->clear();

            return $this->redirectResponse->temporary('acp/menus');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
