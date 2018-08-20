<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Update\Model;

use ACP3\Core\Http\RequestInterface;
use ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Psr\Log\LoggerInterface;
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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * ModuleUpdateModel constructor.
     *
     * @param \ACP3\Installer\Core\Environment\ApplicationPath          $appPath
     * @param \Psr\Log\LoggerInterface                                  $logger
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
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
     * @param RequestInterface $request
     *
     * @throws \Exception
     */
    public function updateContainer(RequestInterface $request)
    {
        $this->container = ServiceContainerBuilder::create(
            $this->logger,
            $this->appPath,
            $request->getSymfonyRequest(),
            $this->container->getParameter('core.environment'),
            true
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function updateModules()
    {
        foreach ($this->container->get('core.modules')->getAllModulesTopSorted() as $moduleInfo) {
            $module = \strtolower($moduleInfo['dir']);
            $this->results[$module] = $this->updateModule($module);
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus.
     *
     * @param string $moduleName
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function updateModule(string $moduleName)
    {
        $result = false;

        $schemaRegistrar = $this->container->get('core.installer.schema_registrar');
        $migrationRegistrar = $this->container->get('core.installer.migration_registrar');
        $serviceIdMigration = $moduleName . '.installer.migration';
        if ($schemaRegistrar->has($moduleName) === true &&
            $migrationRegistrar->has($serviceIdMigration) === true
        ) {
            $moduleSchema = $schemaRegistrar->get($moduleName);
            $moduleMigration = $migrationRegistrar->get($serviceIdMigration);
            if ($this->container->get('core.modules')->isInstalled($moduleName) || \count($moduleMigration->renameModule()) > 0) {
                $result = $this->container->get('core.modules.schemaUpdater')->updateSchema(
                    $moduleSchema,
                    $moduleMigration
                );
            }
        }

        return $result;
    }
}
