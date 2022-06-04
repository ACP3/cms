<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Component\Dto;

use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Modules\ModuleRegistration;

class ComponentDataDto
{
    private string $name;

    /**
     * @param string[] $dependencies
     */
    public function __construct(
        private ComponentTypeEnum $componentType,
        string $componentName,
        private string $path,
        private array $dependencies,
        private ?ModuleRegistration $moduleRegistration = null)
    {
        $this->name = strtolower($componentName);
    }

    public function getComponentType(): ComponentTypeEnum
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

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getModuleRegistration(): ?ModuleRegistration
    {
        return $this->moduleRegistration;
    }
}
