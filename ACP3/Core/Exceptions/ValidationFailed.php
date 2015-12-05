<?php

namespace ACP3\Core\Exceptions;

/**
 * Class ValidationFailed
 * @package ACP3\Core\Exceptions
 */
class ValidationFailed extends \Exception
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
