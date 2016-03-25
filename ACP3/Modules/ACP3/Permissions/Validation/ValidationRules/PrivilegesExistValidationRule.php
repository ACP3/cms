<?php
namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\ACL;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository;

/**
 * Class PrivilegesExistValidationRule
 * @package ACP3\Modules\ACP3\Permissions\Validation\ValidationRules
 */
class PrivilegesExistValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository
     */
    protected $privilegeRepository;

    /**
     * PrivilegesExistValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository $privilegeRepository
     */
    public function __construct(PrivilegeRepository $privilegeRepository)
    {
        $this->privilegeRepository = $privilegeRepository;
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
     * @param array $privilegeIds
     *
     * @return boolean
     */
    public function privilegesExist(array $privilegeIds)
    {
        $allPrivileges = $this->privilegeRepository->getAllPrivileges();
        $valid = false;

        foreach ($allPrivileges as $privilege) {
            $valid = false;
            foreach ($privilegeIds as $module) {
                foreach ($module as $privilegeId => $permission) {
                    if ($privilegeId == $privilege['id']
                        && $permission >= ACL\PermissionEnum::DENY_ACCESS
                        && $permission <= ACL\PermissionEnum::INHERIT_ACCESS
                    ) {
                        $valid = true;
                        break 2;
                    }
                }
            }
        }

        return $valid;
    }
}
