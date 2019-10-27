<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Update\Model;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Installer\Exception\MissingInstallerException;
use ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SchemaUpdateModel
{
    /**
     * @var array
     */
    protected $results = [];
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;

    /**
     * ModuleUpdateModel constructor.
     *
     * @param \ACP3\Installer\Core\Environment\ApplicationPath          $appPath
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(
        ApplicationPath $appPath,
        ContainerInterface $container
    ) {
        $this->container = $container;
        $this->appPath = $appPath;
    }

    /**
     * @param RequestInterface $request
     *
     * @throws \Exception
     */
    public function updateContainer(RequestInterface $request): void
    {
        $this->container = ServiceContainerBuilder::create(
            $this->appPath,
            $request->getSymfonyRequest(),
            $this->container->getParameter('core.environment'),
            true
        );
    }

    /**
     * @return array
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function updateModules(): array
    {
        /** @var \ACP3\Core\Modules $modules */
        $modules = $this->container->get('core.modules');
        foreach ($modules->getAllModulesTopSorted() as $moduleInfo) {
            try {
                $this->updateModule($moduleInfo['name']);

                $this->results[$moduleInfo['name']] = true;
            } catch (\Throwable $e) {
                $this->results[$moduleInfo['name']] = false;
            }
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus.
     *
     * @param string $moduleName
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateModule(string $moduleName): void
    {
        /** @var \ACP3\Core\Modules $modules */
        $modules = $this->container->get('core.modules');

        if (!$modules->isInstallable($moduleName)) {
            return;
        }

        /** @var \ACP3\Core\Installer\SchemaRegistrar $schemaRegistrar */
        $schemaRegistrar = $this->container->get('core.installer.schema_registrar');
        /** @var \ACP3\Core\Installer\MigrationRegistrar $migrationRegistrar */
        $migrationRegistrar = $this->container->get('core.installer.migration_registrar');
        /** @var \ACP3\Core\Modules\SchemaUpdater $schemaUpdater */
        $schemaUpdater = $this->container->get('core.modules.schemaUpdater');

        $serviceIdMigration = $moduleName . '.installer.migration';
        if (!$schemaRegistrar->has($moduleName) || !$migrationRegistrar->has($serviceIdMigration)) {
            throw new MissingInstallerException(
                \sprintf('Could not find any schema or migration files for module "%s"', $moduleName)
            );
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
