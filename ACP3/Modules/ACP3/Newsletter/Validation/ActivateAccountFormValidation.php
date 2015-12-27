<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountExistsByHashValidationRule;

/**
 * Class ActivateAccountFormValidation
 * @package ACP3\Modules\ACP3\Newsletter\Validation
 */
class ActivateAccountFormValidation extends AbstractFormValidation
{

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(
                AccountExistsByHashValidationRule::NAME,
                [
                    'data' => $formData['hash'],
                    'message' => $this->translator->t('newsletter', 'account_not_exists')
                ]);

        $this->validator->validate();
    }
}