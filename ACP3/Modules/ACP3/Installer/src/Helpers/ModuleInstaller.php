<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Modules\ACP3\System\Exception\ModuleInstallerException;
use Psr\Container\ContainerInterface;

class ModuleInstaller
{
    /**
     * @var Install
     */
    private $installHelper;
    /**
     * @var array
     */
    private $installedModules = [];

    public function __construct(Install $installHelper)
    {
        $this->installHelper = $installHelper;
    }

    /**
     * @throws \ACP3\Modules\ACP3\System\Exception\ModuleInstallerException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function installModules(ContainerInterface $container): array
    {
        /** @var \Psr\Container\ContainerInterface $schemaRegistrar */
        $schemaRegistrar = $container->get(SchemaRegistrar::class);

        $installableModules = array_filter(
            ComponentRegistry::excludeByType(ComponentRegistry::allTopSorted(), [ComponentTypeEnum::THEME]),
            static function ($module) use ($schemaRegistrar) {
                return $schemaRegistrar->has($module->getName());
            }
        );

        foreach ($installableModules as $module) {
            if ($this->installHelper->installModule($schemaRegistrar->get($module->getName()), $container) === false) {
                throw new ModuleInstallerException(sprintf('Error while installing module "%s"', $module->getName()));
            }

            $this->installedModules[$module->getName()] = true;
        }

        return $this->installedModules;
    }
}
