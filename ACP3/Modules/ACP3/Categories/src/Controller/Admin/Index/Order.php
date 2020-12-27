<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class Order extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoriesRepository;
    /**
     * @var Core\NestedSet\Operation\Sort
     */
    private $sortOperation;
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
        CategoryRepository $categoriesRepository,
        Psr6Store $httpCacheStore
    ) {
        parent::__construct($context);

        $this->categoriesRepository = $categoriesRepository;
        $this->sortOperation = $sortOperation;
        $this->redirectResponse = $redirectResponse;
        $this->httpCacheStore = $httpCacheStore;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if ($this->categoriesRepository->resultExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->httpCacheStore->clear();

            return $this->redirectResponse->temporary('acp/categories');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
