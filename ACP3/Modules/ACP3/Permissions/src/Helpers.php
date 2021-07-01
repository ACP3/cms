<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Modules\ACP3\Permissions\Model\Repository\UserRoleRepository;

class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\UserRoleRepository
     */
    private $userRoleRepository;

    public function __construct(
        UserRoleRepository $userRoleRepository
    ) {
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateUserRoles(array $roles, int $userId): bool
    {
        $result = $this->userRoleRepository->delete($userId, 'user_id');

        $result2 = false;
        foreach ($roles as $role) {
            $result2 = $this->userRoleRepository->insert(['user_id' => $userId, 'role_id' => $role]);
        }

        return $result !== false && $result2 !== false;
    }
}
