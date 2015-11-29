<?php
namespace ACP3\Modules\ACP3\Permissions\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;
use ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\PrivilegesExistValidationRule;
use ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\RoleNotExistsValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Permissions
 */
class Role extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * Role constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate
    )
    {
        parent::__construct($lang, $validate);

        $this->validator = $validator;
    }

    /**
     * @param array $formData
     * @param int   $roleId
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $roleId = 0)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->lang->t('system', 'name_to_short')
                ])
            ->addConstraint(
                RoleNotExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->lang->t('permissions', 'role_already_exists'),
                    'extra' => [
                        'role_id' => $roleId
                    ]
                ])
            ->addConstraint(
                PrivilegesExistValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'privileges',
                    'message' => $this->lang->t('permissions', 'invalid_privileges')
                ]);

        $this->validator->validate();
    }
}
