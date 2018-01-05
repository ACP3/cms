<?php
namespace ACP3\Modules\ACP3\Permissions\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclPrivilegesRepository;

class PrivilegeExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\AclPrivilegesRepository
     */
    protected $privilegeRepository;

    /**
     * PrivilegeExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\AclPrivilegesRepository $privilegeRepository
     */
    public function __construct(AclPrivilegesRepository $privilegeRepository)
    {
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->privilegeRepository->privilegeExists($data);
    }
}
