<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Validation\Validator;

interface ValidationRuleInterface
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     */
    public function setMessage(string $message): void;

    /**
     * @param \ACP3\Core\Validation\Validator $validator
     * @param mixed                           $data
     * @param string|array                    $field
     * @param array                           $extra
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = []): void;

    /**
     * @param bool|int|float|string|array $data
     * @param string|array                $field
     * @param array                       $extra
     *
     * @return bool
     */
    public function isValid($data, $field = '', array $extra = []): bool;
}
