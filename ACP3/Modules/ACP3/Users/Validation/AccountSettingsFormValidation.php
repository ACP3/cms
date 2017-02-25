<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

class AccountSettingsFormValidation extends AbstractUserFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        $this->validateUserSettings($formData);
        $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');

        $this->validator->validate();
    }
}
