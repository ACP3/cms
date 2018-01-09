<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclUserRolesRepository;
use Doctrine\DBAL\DBALException;

class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository
     */
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\AclUserRolesRepository
     */
    protected $userRoleRepository;

    /**
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository     $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\AclUserRolesRepository $userRoleRepository
     */
    public function __construct(
        AclRolesRepository $roleRepository,
        AclUserRolesRepository $userRoleRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param array $roles
     * @param int   $userId
     *
     * @return bool
     */
    public function updateUserRoles(array $roles, int $userId)
    {
        try {
            $this->userRoleRepository->delete($userId, 'user_id');

            foreach ($roles as $role) {
                $this->userRoleRepository->insert(['user_id' => $userId, 'role_id' => $role]);
            }

            return true;
        } catch (DBALException $e) {
            return false;
        }
    }
}
