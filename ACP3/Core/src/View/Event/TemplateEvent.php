<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TemplateEvent extends Event
{
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $arguments)
    {
        $this->parameters = $arguments;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
