<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Entity;

final class LibraryEntity
{
    /**
     * @var bool
     */
    private $enabled = false;
    /**
     * @var array
     */
    private $dependencies;
    /**
     * @var array
     */
    private $css;
    /**
     * @var array
     */
    private $js;
    /**
     * @var string
     */
    private $moduleName;

    public function __construct(
        private string $libraryIdentifier,
        private bool $enabledForAjax = true,
        array $dependencies = [],
        array $css = [],
        array $js = [],
        string $moduleName = '',
        private bool $deferrableCss = false
    ) {
        if (!$moduleName) {
            throw new \InvalidArgumentException('The argument `moduleName` is required!');
        }
        $this->dependencies = $dependencies;
        $this->css = $css;
        $this->js = $js;
        $this->moduleName = $moduleName;
    }

    public function getLibraryIdentifier(): string
    {
        return $this->libraryIdentifier;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): LibraryEntity
    {
        $libraryDto = clone $this;
        $libraryDto->enabled = true;

        return $libraryDto;
    }

    public function isEnabledForAjax(): bool
    {
        return $this->enabledForAjax;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getCss(): array
    {
        return $this->css;
    }

    public function getJs(): array
    {
        return $this->js;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function isDeferrableCss(): bool
    {
        return $this->deferrableCss;
    }
}
