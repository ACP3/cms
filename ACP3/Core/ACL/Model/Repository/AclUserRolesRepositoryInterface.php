<?php
namespace ACP3\Core\ACL\Model\Repository;

interface AclUserRolesRepositoryInterface
{
    /**
     * @param int $userId
     *
     * @return array
     */
    public function getRolesByUserId($userId);
}
