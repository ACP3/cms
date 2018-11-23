<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Validation\Validator;

abstract class AbstractValidationRule implements ValidationRuleInterface
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * {@inheritdoc}
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = []): void
    {
        if (!$this->isValid($data, $field, $extra)) {
            $validator->addError($this->getMessage(), $field);
        }
    }
}
