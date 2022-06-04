<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Repository\AclResourceRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PermissionsExistValidationRule extends AbstractValidationRule
{
    public function __construct(private AclResourceRepository $resourceRepository)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data) && \is_array($data) && $this->permissionsExist($data);
    }

    /**
     * Checks, whether the given resources with its permission values exist and contain plausible values.
     *
     * @param array<int|string, int|string> $resourcesWithPermissions
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function permissionsExist(array $resourcesWithPermissions): bool
    {
        $resourceIds = array_map(static fn ($resource) => (int) $resource['resource_id'], $this->resourceRepository->getAllResources());

        $permissions = [
            PermissionEnum::PERMIT_ACCESS,
            PermissionEnum::INHERIT_ACCESS,
        ];

        foreach ($resourcesWithPermissions as $resourceId => $permissionValue) {
            if (!\in_array((int) $resourceId, $resourceIds, true) || !\in_array(PermissionEnum::tryFrom((int) $permissionValue), $permissions, true)) {
                return false;
            }
        }

        return true;
    }
}
