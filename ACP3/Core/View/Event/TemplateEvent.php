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

    /**
     * TemplateEvent constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->parameters = $arguments;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
