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
     * @var string
     */
    private $content = '';

    public function __construct(array $arguments)
    {
        $this->parameters = $arguments;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function addContent(string $content): void
    {
        $this->content .= $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
