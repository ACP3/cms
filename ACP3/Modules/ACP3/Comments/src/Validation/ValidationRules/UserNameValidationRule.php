<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;

class UserNameValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Validation\ValidationRules\IntegerValidationRule
     */
    private $integerValidationRule;

    public function __construct(IntegerValidationRule $integerValidationRule)
    {
        $this->integerValidationRule = $integerValidationRule;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $userName = reset($field);
            $userId = next($field);

            return (!empty($data[$userId]) && $this->integerValidationRule->isValid($data[$userId])) || !empty($data[$userName]);
        }

        return false;
    }
}
