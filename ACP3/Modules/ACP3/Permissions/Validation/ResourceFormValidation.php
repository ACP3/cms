<?php
namespace ACP3\Modules\ACP3\Permissions\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\PrivilegeExistsValidationRule;

/**
 * Class ResourceFormValidation
 * @package ACP3\Modules\ACP3\Permissions\Validation
 */
class ResourceFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\ModuleIsInstalledValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'modules',
                    'message' => $this->translator->t('permissions', 'select_module')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'area',
                    'message' => $this->translator->t('permissions', 'type_in_area'),
                    'extra' => [
                        'haystack' => ['admin', 'frontend', 'sidebar']
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'controller',
                    'message' => $this->translator->t('permissions', 'type_in_controller')
                ])
            ->addConstraint(
                PrivilegeExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'privileges',
                    'message' => $this->translator->t('permissions', 'privilege_does_not_exist')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::NAME,
                [
                    'data' => strtolower($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/'),
                    'field' => 'resource',
                    'message' => $this->translator->t('permissions', 'type_in_resource')
                ]
            );

        $this->validator->validate();
    }
}