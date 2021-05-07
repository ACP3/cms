<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\Exceptions;

class ValidationFailedException extends \Exception
{
    /**
     * ValidationFailed constructor.
     *
     * @param array $message
     */
    public function __construct($message)
    {
        parent::__construct(serialize($message));
    }
}
