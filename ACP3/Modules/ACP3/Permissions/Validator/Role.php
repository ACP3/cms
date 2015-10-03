<?php
namespace ACP3\Modules\ACP3\Permissions\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Permissions
 */
class Role extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\ACL
     */
    protected $aclValidator;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;

    /**
     * @param \ACP3\Core\Lang                                     $lang
     * @param \ACP3\Core\Validator\Rules\Misc                     $validate
     * @param \ACP3\Core\Validator\Rules\ACL                      $aclValidator
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository $roleRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\ACL $aclValidator,
        RoleRepository $roleRepository
    )
    {
        parent::__construct($lang, $validate);

        $this->aclValidator = $aclValidator;
        $this->roleRepository = $roleRepository;
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
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['name'])) {
            $this->errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['name']) && $this->roleRepository->roleExistsByName($formData['name'], $roleId) === true) {
            $this->errors['name'] = $this->lang->t('permissions', 'role_already_exists');
        }
        if (empty($formData['privileges']) || is_array($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'no_privilege_selected');
        } elseif ($this->aclValidator->aclPrivilegesExist($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'invalid_privileges');
        }

        $this->_checkForFailedValidation();
    }
}
