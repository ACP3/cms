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
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var Core\NestedSet\Operation\Sort
     */
    protected $sortOperation;

    /**
     * Order constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                  $context
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                           $permissionsCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\NestedSet\Operation\Sort $sortOperation,
        Permissions\Model\Repository\RoleRepository $roleRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->roleRepository = $roleRepository;
        $this->permissionsCache = $permissionsCache;
        $this->sortOperation = $sortOperation;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action)
    {
        if ($this->roleRepository->roleExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->permissionsCache->getCacheDriver()->deleteAll();

            return $this->redirect()->temporary('acp/permissions');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
