<?php
namespace ACP3\Modules\ACP3\Permissions\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;

/**
 * Class RoleNotExistsValidationRule
 * @package ACP3\Modules\ACP3\Permissions\Validator\ValidationRules
 */
class RoleNotExistsValidationRule extends AbstractValidationRule
{
    const NAME = 'permissions_role_exists';

    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;

    /**
     * RoleExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->roleRepository->roleExistsByName($data, isset($extra['role_id']) ? $extra['role_id'] : 0) === false;
    }
}