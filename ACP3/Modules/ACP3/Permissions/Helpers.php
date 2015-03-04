<?php
namespace ACP3\Modules\ACP3\Permissions;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Permissions
 */
class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @param \ACP3\Modules\ACP3\Permissions\Model $permissionsModel
     */
    public function __construct(Model $permissionsModel)
    {
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * @param array $roles
     * @param       $userId
     *
     * @return bool
     */
    public function updateUserRoles(array $roles, $userId)
    {
        $bool = $this->permissionsModel->delete($userId, 'user_id', Model::TABLE_NAME_USER_ROLES);

        $bool2 = false;
        foreach ($roles as $role) {
            $bool2 = $this->permissionsModel->insert(['user_id' => $userId, 'role_id' => $role], Model::TABLE_NAME_USER_ROLES);
        }

        return $bool !== false && $bool2 !== false;
    }
}