<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RolesExistValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly ACL $acl)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return \is_array($data) && $this->aclRolesExist($data);
    }

    /**
     * @param int[]|string[] $roleIds
     */
    private function aclRolesExist(array $roleIds): bool
    {
        $allRoles = $this->acl->getAllRoles();
        $good = [];
        foreach ($allRoles as $row) {
            $good[] = (int) $row['id'];
        }

        foreach ($roleIds as $roleId) {
            if (\in_array((int) $roleId, $good, true) === false) {
                return false;
            }
        }

        return true;
    }
}
