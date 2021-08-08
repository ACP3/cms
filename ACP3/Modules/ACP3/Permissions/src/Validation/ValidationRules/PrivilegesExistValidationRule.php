<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Repository\AclPrivilegeRepository;

class PrivilegesExistValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Repository\AclPrivilegeRepository
     */
    private $privilegeRepository;

    public function __construct(AclPrivilegeRepository $privilegeRepository)
    {
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data) && \is_array($data) && $this->privilegesExist($data);
    }

    /**
     * Überprüft, ob die übergebenen Privilegien existieren und
     * plausible Werte enthalten.
     */
    private function privilegesExist(array $privilegeIds): bool
    {
        $valid = false;
        foreach ($this->privilegeRepository->getAllPrivileges() as $privilege) {
            $valid = false;
            foreach ($privilegeIds as $module) {
                foreach ($module as $privilegeId => $permission) {
                    if ($this->isValidPrivilege($privilegeId, $privilege, $permission)) {
                        $valid = true;

                        break 2;
                    }
                }
            }
        }

        return $valid;
    }

    private function isValidPrivilege(int $privilegeId, array $privilege, int $permission): bool
    {
        return $privilegeId === (int) $privilege['id']
        && $permission >= PermissionEnum::DENY_ACCESS
        && $permission <= PermissionEnum::INHERIT_ACCESS;
    }
}
