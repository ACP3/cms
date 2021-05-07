<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Model;

use ACP3\Core\Installer\Exception\MissingInstallerException;
use ACP3\Modules\ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class SchemaUpdateModel
{
    /**
     * @var array
     */
    protected $results = [];
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        ApplicationPath $appPath,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->container = $container;
        $this->appPath = $appPath;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function updateContainer(): void
    {
        $this->container = ServiceContainerBuilder::create(
            $this->appPath,
            true
        );
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function updateModules(): array
    {
        /** @var \ACP3\Core\Modules $modules */
        $modules = $this->container->get('core.modules');
        foreach ($modules->getAllModulesTopSorted() as $moduleInfo) {
            if (!$modules->isInstallable($moduleInfo['name'])) {
                continue;
            }

            try {
                $this->updateModule($moduleInfo['name']);

                $this->results[$moduleInfo['name']] = true;
            } catch (\Throwable $e) {
                $this->results[$moduleInfo['name']] = false;

                $this->logger->error($e);
            }
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateModule(string $moduleName): void
    {
        /** @var \ACP3\Core\Modules $modules */
        $modules = $this->container->get('core.modules');

        if (!$modules->isInstallable($moduleName)) {
            return;
        }

        /** @var ContainerInterface $schemaRegistrar */
        $schemaRegistrar = $this->container->get('core.installer.schema_registrar');
        /** @var ContainerInterface $migrationRegistrar */
        $migrationRegistrar = $this->container->get('core.installer.migration_registrar');
        /** @var \ACP3\Core\Modules\SchemaUpdater $schemaUpdater */
        $schemaUpdater = $this->container->get('core.modules.schemaUpdater');

        $serviceIdMigration = $moduleName . '.installer.migration';
        if (!$schemaRegistrar->has($moduleName) || !$migrationRegistrar->has($serviceIdMigration)) {
            throw new MissingInstallerException(sprintf('Could not find any schema or migration files for module "%s"', $moduleName));
        }

        $moduleSchema = $schemaRegistrar->get($moduleName);
        $moduleMigration = $migrationRegistrar->get($serviceIdMigration);
        if ($modules->isInstalled($moduleName) || \count($moduleMigration->renameModule()) > 0) {
            $schemaUpdater->updateSchema(
                $moduleSchema,
                $moduleMigration
            );
        }
    }
}
