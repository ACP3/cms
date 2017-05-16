<?php
namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclUserRolesRepository;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Permissions
 */
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
     * @param       $userId
     *
     * @return bool
     */
    public function updateUserRoles(array $roles, $userId)
    {
        $bool = $this->userRoleRepository->delete($userId, 'user_id');

        $bool2 = false;
        foreach ($roles as $role) {
            $bool2 = $this->userRoleRepository->insert(['user_id' => $userId, 'role_id' => $role]);
        }

        return $bool !== false && $bool2 !== false;
    }
}
