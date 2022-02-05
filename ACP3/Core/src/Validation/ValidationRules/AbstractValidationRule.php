<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Validation\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractValidationRule implements ValidationRuleInterface
{
    private string $message = '';

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
    public function setMessage(string $message): ValidationRuleInterface
    {
        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Validator $validator, bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): void
    {
        if (!$this->isValid($data, $field, $extra)) {
            $validator->addError($this->getMessage(), $field);
        }
    }
}
