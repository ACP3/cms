<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

class RolesExistValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;

    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return \is_array($data) && $this->aclRolesExist($data);
    }

    private function aclRolesExist(array $roles): bool
    {
        $allRoles = $this->acl->getAllRoles();
        $good = [];
        foreach ($allRoles as $row) {
            $good[] = $row['id'];
        }

        foreach ($roles as $row) {
            if (\in_array($row, $good) === false) {
                return false;
            }
        }

        return true;
    }
}
