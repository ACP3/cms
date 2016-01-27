<?php
namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class RolesExistValidationRule
 * @package ACP3\Modules\ACP3\Permissions\Validation\ValidationRules
 */
class RolesExistValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;

    /**
     * RolesExistValidationRule constructor.
     *
     * @param \ACP3\Core\ACL $acl
     */
    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return is_array($data) ? $this->aclRolesExist($data) : false;
    }

    /**
     * @param array $roles
     *
     * @return boolean
     */
    protected function aclRolesExist(array $roles)
    {
        $allRoles = $this->acl->getAllRoles();
        $good = [];
        foreach ($allRoles as $row) {
            $good[] = $row['id'];
        }

        foreach ($roles as $row) {
            if (in_array($row, $good) === false) {
                return false;
            }
        }
        return true;
    }
}