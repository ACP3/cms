<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Services;

use ACP3\Modules\ACP3\Permissions\Model\AclPermissionModel;
use ACP3\Modules\ACP3\Permissions\Model\AclRoleModel;
use ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation;

class RoleUpsertService
{
    public function __construct(private readonly RoleFormValidation $roleFormValidation, private readonly AclRoleModel $aclRoleModel, private readonly AclPermissionModel $aclPermissionModel)
    {
    }

    /**
     * @param array<string, mixed> $updatedData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function upsert(array $updatedData, ?int $roleId = null): int
    {
        $this->roleFormValidation
            ->withRoleId($roleId)
            ->validate($updatedData);

        $updatedData['parent_id'] = $roleId === 1 ? 0 : $updatedData['parent_id'];

        $result = $this->aclRoleModel->save($updatedData, $roleId);
        $this->aclPermissionModel->updatePermissions($updatedData['resources'], $roleId);

        return $result;
    }
}
