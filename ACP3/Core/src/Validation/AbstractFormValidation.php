<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation;

use ACP3\Core;

abstract class AbstractFormValidation
{
    public function __construct(protected Core\I18n\Translator $translator, protected Core\Validation\Validator $validator)
    {
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @throws Exceptions\ValidationRuleNotFoundException
     * @throws Exceptions\InvalidFormTokenException
     * @throws Exceptions\ValidationFailedException
     */
    abstract public function validate(array $formData): void;
}
