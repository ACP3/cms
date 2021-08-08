<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Repository\AclResourceRepository;

class PermissionsExistValidationRule extends AbstractValidationRule
{
    /**
     * @var AclResourceRepository
     */
    private $resourceRepository;

    public function __construct(AclResourceRepository $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data) && \is_array($data) && $this->permissionsExist($data);
    }

    /**
     * Checks, whether the given resources with its permission values exist and contain plausible values.
     */
    public function permissionsExist(array $resourcesWithPermissions): bool
    {
        $resourceIds = array_map(static function ($resource) {
            return (int) $resource['resource_id'];
        }, $this->resourceRepository->getAllResources());

        $permissions = [
            PermissionEnum::PERMIT_ACCESS,
            PermissionEnum::INHERIT_ACCESS,
        ];

        foreach ($resourcesWithPermissions as $resourceId => $permissionValue) {
            if (!\in_array((int) $resourceId, $resourceIds, true) || !\in_array((int) $permissionValue, $permissions, true)) {
                return false;
            }
        }

        return true;
    }
}
