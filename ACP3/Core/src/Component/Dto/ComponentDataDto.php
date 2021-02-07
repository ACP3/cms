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
    private $componentType;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $path;
    /**
     * @var array
     */
    private $dependencies;
    /**
     * @var \ACP3\Core\Modules\ModuleRegistration|null
     */
    private $moduleRegistration;

    public function __construct(
        string $componentType,
        string $componentName,
        string $componentPath,
        array $dependencies,
        ?ModuleRegistration $moduleRegistration = null)
    {
        $this->componentType = $componentType;
        $this->name = \strtolower($componentName);
        $this->path = $componentPath;
        $this->dependencies = $dependencies;
        $this->moduleRegistration = $moduleRegistration;
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
