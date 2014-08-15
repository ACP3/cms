<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 21.06.14
 * Time: 23:57
 */

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
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var Model
     */
    protected $permissionsModel;

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\ACL $aclValidator,
        Core\Validator\Rules\Router $routerValidator,
        Core\Modules $modules,
        Core\Request $request,
        Model $permissionsModel
    )
    {
        parent::__construct($lang, $validate);

        $this->aclValidator = $aclValidator;
        $this->routerValidator = $routerValidator;
        $this->modules = $modules;
        $this->request = $request;
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['name']) && $this->permissionsModel->roleExistsByName($formData['name']) === true) {
            $errors['name'] = $this->lang->t('permissions', 'role_already_exists');
        }
        if (empty($formData['privileges']) || is_array($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'no_privilege_selected');
        }
        if (!empty($formData['privileges']) && $this->aclValidator->aclPrivilegesExist($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'invalid_privileges');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreateResource(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['modules']) || $this->modules->isInstalled($formData['modules']) === false) {
            $errors['modules'] = $this->lang->t('permissions', 'select_module');
        }
        if (empty($formData['area']) || in_array($formData['area'], array('admin', 'frontend', 'sidebar')) === false) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_area');
        }
        if (empty($formData['controller'])) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_controller');
        }
        if (empty($formData['resource']) || preg_match('=/=', $formData['resource']) || $this->routerValidator->isInternalURI(strtolower($formData['modules']) . '/' . $formData['controller'] . '/' . $formData['resource'] . '/') === false) {
            $errors['resource'] = $this->lang->t('permissions', 'type_in_resource');
        }
        if (empty($formData['privileges']) || $this->validate->isNumber($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'select_privilege');
        }
        if ($this->validate->isNumber($formData['privileges']) && $this->permissionsModel->resourceExists($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'privilege_does_not_exist');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['name']) && $this->permissionsModel->roleExistsByName($formData['name'], $this->request->id) === true) {
            $errors['name'] = $this->lang->t('permissions', 'role_already_exists');
        }
        if (empty($formData['privileges']) || is_array($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'no_privilege_selected');
        }
        if (!empty($formData['privileges']) && $this->aclValidator->aclPrivilegesExist($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'invalid_privileges');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEditResource(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['modules']) || $this->modules->isInstalled($formData['modules']) === false) {
            $errors['modules'] = $this->lang->t('permissions', 'select_module');
        }
        if (empty($formData['area']) || in_array($formData['area'], array('admin', 'frontend', 'sidebar')) === false) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_area');
        }
        if (empty($formData['controller'])) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_controller');
        }
        if (empty($formData['resource']) || preg_match('=/=', $formData['resource']) || $this->routerValidator->isInternalURI($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/') === false) {
            $errors['resource'] = $this->lang->t('permissions', 'type_in_resource');
        }
        if (empty($formData['privileges']) || $this->validate->isNumber($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'select_privilege');
        }
        if ($this->validate->isNumber($formData['privileges']) && $this->permissionsModel->resourceExists($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'privilege_does_not_exist');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 