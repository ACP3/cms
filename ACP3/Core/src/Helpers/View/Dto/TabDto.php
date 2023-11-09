<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View\Dto;

class TabDto
{
    public function __construct(private readonly string $title, private readonly string $content, private readonly ?string $name = null)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
