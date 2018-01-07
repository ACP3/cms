<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

class RolesExistValidationRule extends AbstractValidationRule
{
    /**
     * @var ACLInterface
     */
    protected $acl;

    /**
     * RolesExistValidationRule constructor.
     *
     * @param ACLInterface $acl
     */
    public function __construct(ACLInterface $acl)
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

        return \is_array($data) ? $this->aclRolesExist($data) : false;
    }

    /**
     * @param array $roles
     *
     * @return bool
     */
    protected function aclRolesExist(array $roles)
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
