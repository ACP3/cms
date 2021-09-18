<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Migration\MigrationServiceLocator;
use ACP3\Core\Migration\Repository\MigrationRepositoryInterface;
use ACP3\Modules\ACP3\System\Exception\ModuleInstallerException;
use Psr\Container\ContainerInterface;

class ModuleInstaller
{
    /**
     * @var Install
     */
    private $installHelper;
    /**
     * @var MigrationServiceLocator
     */
    private $migrationServiceLocator;
    /**
     * @var MigrationRepositoryInterface
     */
    private $migrationRepository;

    public function __construct(
        Install $installHelper,
        MigrationServiceLocator $migrationServiceLocator,
        MigrationRepositoryInterface $migrationRepository
    ) {
        $this->installHelper = $installHelper;
        $this->migrationServiceLocator = $migrationServiceLocator;
        $this->migrationRepository = $migrationRepository;
    }

    /**
     * @throws \ACP3\Modules\ACP3\System\Exception\ModuleInstallerException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function installModules(ContainerInterface $container): array
    {
        $installedModules = $this->doInstallModules($container);

        $this->doMarkAllMigrationsAsExecuted();

        return $installedModules;
    }

    /**
     * @return array<string, boolean>
     *
     * @throws ModuleInstallerException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function doInstallModules(ContainerInterface $container): array
    {
        /** @var \Psr\Container\ContainerInterface $schemaRegistrar */
        $schemaRegistrar = $container->get(SchemaRegistrar::class);

        $installableModules = array_filter(
            ComponentRegistry::excludeByType(ComponentRegistry::allTopSorted(), [ComponentTypeEnum::THEME]),
            static function ($module) use ($schemaRegistrar) {
                return $schemaRegistrar->has($module->getName());
            }
        );

        $installedModules = [];
        foreach ($installableModules as $module) {
            if ($this->installHelper->installModule($schemaRegistrar->get($module->getName()), $container) === false) {
                throw new ModuleInstallerException(sprintf('Error while installing module "%s"', $module->getName()));
            }

            $installedModules[$module->getName()] = true;
        }

        return $installedModules;
    }

    /**
     * Installing a module already ensures, that it is using the most current DB schema.
     * Therefore, we have to ensure that all the available migrations are getting marked as executed.
     * Otherwise, these migrations would get executed the next time the DB schema updater gets executed which could result in errors.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function doMarkAllMigrationsAsExecuted(): void
    {
        foreach ($this->migrationServiceLocator->getMigrations() as $fqcn => $migration) {
            $this->migrationRepository->insert(['name' => $fqcn]);
        }
    }
}
