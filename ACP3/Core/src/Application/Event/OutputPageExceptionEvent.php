<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

class OutputPageExceptionEvent extends Event
{
    public const NAME = 'core.output_page_exception';

    /**
     * @var \Throwable
     */
    private $throwable;

    public function __construct(\Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }
}
