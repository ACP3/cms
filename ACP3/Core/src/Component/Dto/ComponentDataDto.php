<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Component\Dto;

use ACP3\Core\Modules\ModuleRegistration;

class ComponentDataDto
{
    /**
     * @var string
     */
    private $name;

    public function __construct(
        private string $componentType,
        string $componentName,
        private string $path,
        private array $dependencies,
        private ?ModuleRegistration $moduleRegistration = null)
    {
        $this->name = strtolower($componentName);
    }

    public function getComponentType(): string
    {
        return $this->componentType;
    }

    /**
     * Returns the lower cased component name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getModuleRegistration(): ?ModuleRegistration
    {
        return $this->moduleRegistration;
    }
}
