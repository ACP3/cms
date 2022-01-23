<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TemplateEvent extends Event
{
    private string $content = '';

    /**
     * @param mixed[] $parameters
     */
    public function __construct(private array $parameters)
    {
    }

    /**
     * @return mixed[]
     */
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
