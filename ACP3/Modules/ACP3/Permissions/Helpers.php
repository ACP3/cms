<?php
namespace ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Permissions
 */
class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;

    /**
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param array $roles
     * @param       $userId
     *
     * @return bool
     */
    public function updateUserRoles(array $roles, $userId)
    {
        $bool = $this->roleRepository->delete($userId, 'user_id', RoleRepository::TABLE_NAME_USER_ROLES);

        $bool2 = false;
        foreach ($roles as $role) {
            $bool2 = $this->roleRepository->insert(['user_id' => $userId, 'role_id' => $role], RoleRepository::TABLE_NAME_USER_ROLES);
        }

        return $bool !== false && $bool2 !== false;
    }
}