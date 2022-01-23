<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters\Event;

use Symfony\Contracts\EventDispatcher\Event;

class JsSvgIconEvent extends Event
{
    /**
     * @var array<string, string>
     */
    private array $jsSvgIcons = [];

    public function addIcon(string $iconIdentifier, string $iconPath): void
    {
        if (!\array_key_exists($iconIdentifier, $this->jsSvgIcons)) {
            $this->jsSvgIcons[$iconIdentifier] = $iconPath;
        }
    }

    /**
     * @return array<string, string>
     */
    public function getIcons(): array
    {
        return $this->jsSvgIcons;
    }
}
