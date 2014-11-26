<?php
namespace ACP3\Modules\Permissions;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Permissions
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\ACL
     */
    protected $aclValidator;
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Model
     */
    protected $permissionsModel;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\ACL $aclValidator
     * @param Core\Validator\Rules\Router $routerValidator
     * @param Core\Modules $modules
     * @param Model $permissionsModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\ACL $aclValidator,
        Core\Validator\Rules\Router $routerValidator,
        Core\Modules $modules,
        Model $permissionsModel
    ) {
        parent::__construct($lang, $validate);

        $this->aclValidator = $aclValidator;
        $this->routerValidator = $routerValidator;
        $this->modules = $modules;
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * @param array $formData
     * @param int $roleId
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
        if (!empty($formData['name']) && $this->permissionsModel->roleExistsByName($formData['name'], $roleId) === true) {
            $this->errors['name'] = $this->lang->t('permissions', 'role_already_exists');
        }
        if (empty($formData['privileges']) || is_array($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'no_privilege_selected');
        }
        if (!empty($formData['privileges']) && $this->aclValidator->aclPrivilegesExist($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'invalid_privileges');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateResource(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['modules']) || $this->modules->isInstalled($formData['modules']) === false) {
            $this->errors['modules'] = $this->lang->t('permissions', 'select_module');
        }
        if (empty($formData['area']) || in_array($formData['area'], ['admin', 'frontend', 'sidebar']) === false) {
            $this->errors['controller'] = $this->lang->t('permissions', 'type_in_area');
        }
        if (empty($formData['controller'])) {
            $this->errors['controller'] = $this->lang->t('permissions', 'type_in_controller');
        }
        if (empty($formData['resource']) || preg_match('=/=', $formData['resource']) || $this->routerValidator->isInternalURI($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/') === false) {
            $this->errors['resource'] = $this->lang->t('permissions', 'type_in_resource');
        }
        if (empty($formData['privileges']) || $this->validate->isNumber($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'select_privilege');
        }
        if ($this->validate->isNumber($formData['privileges']) && $this->permissionsModel->resourceExists($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'privilege_does_not_exist');
        }

        $this->_checkForFailedValidation();
    }
}
