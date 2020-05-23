<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Order extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository
     */
    private $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    private $permissionsCache;
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
        Permissions\Model\Repository\RoleRepository $roleRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->roleRepository = $roleRepository;
        $this->permissionsCache = $permissionsCache;
        $this->sortOperation = $sortOperation;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if ($this->roleRepository->roleExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->permissionsCache->getCacheDriver()->deleteAll();

            return $this->redirectResponse->temporary('acp/permissions');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
