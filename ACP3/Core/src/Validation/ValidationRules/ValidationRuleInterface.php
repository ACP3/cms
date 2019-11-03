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
    public function getMessage();

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * @param mixed  $data
     * @param string $field
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = []);

    /**
     * @param bool|int|float|string|array $data
     * @param string                      $field
     *
     * @return bool
     */
    public function isValid($data, $field = '', array $extra = []);
}
