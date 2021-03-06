<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\Model;

use ACP3\Core\Installer\Exception\MissingInstallerException;
use ACP3\Core\Modules;
use ACP3\Core\Modules\SchemaUpdater;
use ACP3\Core\XML;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class SchemaUpdateModel
{
    use Modules\ModuleDependenciesTrait;

    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var SchemaUpdater
     */
    private $schemaUpdater;
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $schemaLocator;
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $migrationLocator;
    /**
     * @var \ACP3\Core\XML
     */
    private $xml;
    /**
     * @var array
     */
    private $results = [];
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $schemaLocator,
        ContainerInterface $migrationLocator,
        Modules $modules,
        SchemaUpdater $schemaUpdater,
        XML $xml
    ) {
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
        $this->schemaLocator = $schemaLocator;
        $this->migrationLocator = $migrationLocator;
        $this->xml = $xml;
        $this->logger = $logger;
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function updateModules(): array
    {
        foreach ($this->modules->getAllModulesTopSorted() as $moduleInfo) {
            $moduleName = strtolower($moduleInfo['name']);

            if (!$this->modules->isInstallable($moduleName)) {
                continue;
            }

            try {
                $this->updateModule($moduleName);

                $this->results[$moduleName] = true;
            } catch (\Throwable $e) {
                $this->results[$moduleName] = false;

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
     * @throws \ACP3\Core\Installer\Exception\MissingInstallerException
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     */
    private function updateModule(string $moduleName): void
    {
        if (!$this->modules->isInstallable($moduleName)) {
            return;
        }

        $serviceIdMigration = $moduleName . '.installer.migration';
        if (!$this->schemaLocator->has($moduleName) || !$this->migrationLocator->has($serviceIdMigration)) {
            throw new MissingInstallerException(sprintf('Could not find any schema or migration files for module "%s"', $moduleName));
        }

        $moduleSchema = $this->schemaLocator->get($moduleName);
        $moduleMigration = $this->migrationLocator->get($serviceIdMigration);
        if ($this->modules->isInstalled($moduleName) || \count($moduleMigration->renameModule()) > 0) {
            $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
        }
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
