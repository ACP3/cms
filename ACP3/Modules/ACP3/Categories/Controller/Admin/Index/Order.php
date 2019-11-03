<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

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
     * Order constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\NestedSet\Operation\Sort           $sortOperation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\NestedSet\Operation\Sort $sortOperation,
        CategoryRepository $categoriesRepository
    ) {
        parent::__construct($context);

        $this->categoriesRepository = $categoriesRepository;
        $this->sortOperation = $sortOperation;
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

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirect()->temporary('acp/categories');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
