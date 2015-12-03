<?php
namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository;

/**
 * Class PrivilegeExistsValidationRule
 * @package ACP3\Modules\ACP3\Permissions\Validation\ValidationRules
 */
class PrivilegeExistsValidationRule extends AbstractValidationRule
{
    const NAME = 'permissions_privilege_exists';

    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository
     */
    protected $privilegeRepository;

    /**
     * PrivilegeExistsValidationRule constructor.
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

        return $this->privilegeRepository->privilegeExists($data);
    }
}