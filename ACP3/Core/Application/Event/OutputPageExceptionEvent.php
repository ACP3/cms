<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Component\EventDispatcher\Event;

class OutputPageExceptionEvent extends Event
{
    /**
     * @var \Throwable
     */
    private $throwable;

    public function __construct(\Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }
}
