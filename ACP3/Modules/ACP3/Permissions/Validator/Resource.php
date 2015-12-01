<?php
namespace ACP3\Modules\ACP3\Permissions\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\PrivilegeExistsValidationRule;

/**
 * Class Resource
 * @package ACP3\Modules\ACP3\Permissions\Validator
 */
class Resource extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\ModuleIsInstalledValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'modules',
                    'message' => $this->lang->t('permissions', 'select_module')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'area',
                    'message' => $this->lang->t('permissions', 'type_in_area'),
                    'extra' => [
                        'haystack' => ['admin', 'frontend', 'sidebar']
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'controller',
                    'message' => $this->lang->t('permissions', 'type_in_controller')
                ])
            ->addConstraint(
                PrivilegeExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'privileges',
                    'message' => $this->lang->t('permissions', 'privilege_does_not_exist')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InternalUriValidationRule::NAME,
                [
                    'data' => strtolower($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/'),
                    'field' => 'resource',
                    'message' => $this->lang->t('permissions', 'type_in_resource')
                ]
            );

        $this->validator->validate();
    }
}