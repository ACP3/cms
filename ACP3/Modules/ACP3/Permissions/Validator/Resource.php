<?php
namespace ACP3\Modules\ACP3\Permissions\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;

/**
 * Class Resource
 * @package ACP3\Modules\ACP3\Permissions\Validator
 */
class Resource extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository
     */
    protected $privilegeRepository;

    /**
     * @param \ACP3\Core\Lang                                          $lang
     * @param \ACP3\Core\Validator\Rules\Misc                          $validate
     * @param \ACP3\Core\Validator\Rules\Router                        $routerValidator
     * @param \ACP3\Core\Modules                                       $modules
     * @param \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository $privilegeRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router $routerValidator,
        Core\Modules $modules,
        PrivilegeRepository $privilegeRepository
    )
    {
        parent::__construct($lang, $validate);

        $this->routerValidator = $routerValidator;
        $this->modules = $modules;
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
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
        if (empty($formData['resource']) || preg_match('=/=', $formData['resource']) || $this->routerValidator->isInternalURI(strtolower($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/')) === false) {
            $this->errors['resource'] = $this->lang->t('permissions', 'type_in_resource');
        }
        if (empty($formData['privileges']) || $this->validate->isNumber($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'select_privilege');
        } elseif ($this->privilegeRepository->privilegeExists($formData['privileges']) === false) {
            $this->errors['privileges'] = $this->lang->t('permissions', 'privilege_does_not_exist');
        }

        $this->_checkForFailedValidation();
    }
}