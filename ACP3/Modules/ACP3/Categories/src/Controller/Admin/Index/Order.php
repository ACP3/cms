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
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Core\NestedSet\Operation\Sort $sortOperation,
        CategoryRepository $categoriesRepository
    ) {
        parent::__construct($context);

        $this->categoriesRepository = $categoriesRepository;
        $this->sortOperation = $sortOperation;
        $this->redirectResponse = $redirectResponse;
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

            return $this->redirectResponse->temporary('acp/categories');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
