<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Order
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
class Order extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * Order constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext          $context
     * @param \ACP3\Core\NestedSet                                $nestedSet
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                $permissionsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\NestedSet $nestedSet,
        Permissions\Model\RoleRepository $roleRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->roleRepository = $roleRepository;
        $this->permissionsCache = $permissionsCache;
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
        if ($this->roleRepository->roleExists($id) === true) {
            $this->nestedSet->sort(
                $id,
                $action,
                Permissions\Model\RoleRepository::TABLE_NAME
            );

            $this->permissionsCache->getCacheDriver()->deleteAll();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirect()->temporary('acp/permissions');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
