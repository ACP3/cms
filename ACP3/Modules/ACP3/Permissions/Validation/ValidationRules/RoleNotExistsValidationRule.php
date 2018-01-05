<?php
namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository;

class RoleNotExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository
     */
    protected $roleRepository;

    /**
     * RoleExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\AclRolesRepository $roleRepository
     */
    public function __construct(AclRolesRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->roleRepository->roleExistsByName($data, $extra['role_id'] ?? 0) === false;
    }
}
