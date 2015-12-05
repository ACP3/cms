<?php
namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class PrivilegesExistValidationRule
 * @package ACP3\Modules\ACP3\Permissions\Validation\ValidationRules
 */
class PrivilegesExistValidationRule extends AbstractValidationRule
{
    const NAME = 'permissions_privileges_exist';

    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;

    /**
     * PrivilegesExistValidationRule constructor.
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

        return !empty($data) && is_array($data) ? $this->privilegesExist($data) : false;
    }

    /**
     * Überprüft, ob die übergebenen Privilegien existieren und
     * plausible Werte enthalten
     *
     * @param array $privileges
     *    Array mit den IDs der zu überprüfenden Privilegien mit ihren Berechtigungen
     *
     * @return boolean
     */
    public function privilegesExist(array $privileges)
    {
        $allPrivileges = $this->acl->getAllPrivileges();
        $c_allPrivileges = count($allPrivileges);
        $valid = false;

        for ($i = 0; $i < $c_allPrivileges; ++$i) {
            $valid = false;
            foreach ($privileges as $module) {
                foreach ($module as $privilegeId => $value) {
                    if ($privilegeId == $allPrivileges[$i]['id'] && $value >= 0 && $value <= 2) {
                        $valid = true;
                        break 2;
                    }
                }
            }
        }
        return $valid;
    }
}