<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\RolesExistValidationRule;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Users\Validation
 */
class AdminFormValidation extends AbstractUserFormValidation
{
    /**
     * @var int
     */
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
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                RolesExistValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'roles',
                    'message' => $this->translator->t('users', 'select_access_level')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'super_user',
                    'message' => $this->translator->t('users', 'select_super_user'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        $this->validateAccountCoreData($formData, $this->userId);
        $this->validateUserSettings($formData, 1, 1);

        if (isset($formData['new_pwd'])) {
            $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');
        } else {
            $this->validatePassword($formData, 'pwd', 'pwd_repeat');
        }

        $this->validator->validate();
    }
}
