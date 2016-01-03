<?php

namespace ACP3\Core\Validation\Exceptions;

/**
 * Class ValidationFailedException
 * @package ACP3\Core\Validation\Exceptions
 */
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
