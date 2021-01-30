<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers\Navigation;

class NavigationStep
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var bool
     */
    private $isActive;
    /**
     * @var bool
     */
    private $isComplete;

    public function __construct(string $title, bool $isActive, bool $isComplete)
    {
        $this->title = $title;
        $this->isActive = $isActive;
        $this->isComplete = $isComplete;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isComplete(): bool
    {
        return $this->isComplete;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setIsComplete(bool $isComplete): void
    {
        $this->isComplete = $isComplete;
    }
}
