<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core;

/**
 * Class ACL
 * @package ACP3\Core\Validator\Rules
 */
class ACL
{
    /**
     * @var Core\ACL
     */
    protected $acl;

    public function __construct(Core\ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * Überprüft, ob die übergebenen Privilegien existieren und
     * plausible Werte enthalten
     *
     * @param array $privileges
     *    Array mit den IDs der zu überprüfenden Privilegien mit ihren Berechtigungen
     * @return boolean
     */
    public function aclPrivilegesExist(array $privileges)
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
                    }
                }
            }
        }
        return $valid;
    }

    /**
     * Überprüft, ob die selektierten Rollen existieren
     *
     * @param array $roles
     *    Die zu überprüfenden Rollen
     * @return boolean
     */
    public function aclRolesExist(array $roles)
    {
        $allRoles = $this->acl->getAllRoles();
        $good = array();
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