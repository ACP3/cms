<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\PrivilegesExistValidationRule;
use ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\RolesExistValidationRule;

/**
 * Class ACL
 * @package ACP3\Core\Validator\Rules
 *
 * @deprecated
 */
class ACL
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\PrivilegesExistValidationRule
     */
    protected $privilegesExistValidationRule;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\RolesExistValidationRule
     */
    protected $rolesExistValidationRule;

    /**
     * ACL constructor.
     *
     * @param \ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\PrivilegesExistValidationRule $privilegesExistValidationRule
     * @param \ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\RolesExistValidationRule      $rolesExistValidationRule
     */
    public function __construct(
        PrivilegesExistValidationRule $privilegesExistValidationRule,
        RolesExistValidationRule $rolesExistValidationRule
    )
    {
        $this->privilegesExistValidationRule = $privilegesExistValidationRule;
        $this->rolesExistValidationRule = $rolesExistValidationRule;
    }

    /**
     * Überprüft, ob die übergebenen Privilegien existieren und
     * plausible Werte enthalten
     *
     * @param array $privileges
     *    Array mit den IDs der zu überprüfenden Privilegien mit ihren Berechtigungen
     *
     * @return boolean
     *
     * @deprecated
     */
    public function aclPrivilegesExist(array $privileges)
    {
        return $this->privilegesExistValidationRule->isValid($privileges);
    }

    /**
     * Überprüft, ob die selektierten Rollen existieren
     *
     * @param array $roles
     *    Die zu überprüfenden Rollen
     *
     * @return boolean
     */
    public function aclRolesExist(array $roles)
    {
        return $this->rolesExistValidationRule->isValid($roles);
    }
}
