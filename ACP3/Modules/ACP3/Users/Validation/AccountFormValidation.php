<?php

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

/**
 * Class AccountFormValidation
 * @package ACP3\Modules\ACP3\Users\Validation
 */
class AccountFormValidation extends AbstractUserFormValidation
{
    protected $userId = 0;

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);
        ;

        $this->validateAccountCoreData($formData, $this->userId);
        $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');

        $this->validator->validate();
    }
}
