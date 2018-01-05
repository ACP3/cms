<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation;

use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountExistsByHashValidationRule;

class ActivateAccountFormValidation extends AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(
                AccountExistsByHashValidationRule::class,
                [
                    'data' => $formData['hash'],
                    'message' => $this->translator->t('newsletter', 'account_not_exists'),
                ]
            );

        $this->validator->validate();
    }
}
