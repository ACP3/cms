<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Update\Model;


use ACP3\Core\Filesystem;
use ACP3\Core\Modules;
use ACP3\Core\Modules\Installer\MigrationInterface;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\XML;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SchemaUpdateModel
{
    use Modules\ModuleDependenciesTrait;

    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var Modules\SchemaUpdater
     */
    protected $schemaUpdater;
    /**
     * @var Modules\Vendor
     */
    protected $vendor;
    /**
     * @var ApplicationPath
     */
    protected $applicationPath;
    /**
     * @var XML
     */
    protected $xml;
    /**
     * @var array
     */
    protected $results = [];

    /**
     * ModuleUpdateModel constructor.
     * @param ApplicationPath $applicationPath
     * @param XML $xml
     * @param Modules\Vendor $vendor
     * @param Modules $modules
     * @param Modules\SchemaUpdater $schemaUpdater
     */
    public function __construct(
        ApplicationPath $applicationPath,
        XML $xml,
        Modules\Vendor $vendor,
        Modules $modules,
        Modules\SchemaUpdater $schemaUpdater)
    {
        $this->applicationPath = $applicationPath;
        $this->xml = $xml;
        $this->vendor = $vendor;
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
    }

    /**
     * @param ContainerInterface $container
     * @param array $modules
     * @return array
     * @throws \Exception
     */
    public function updateModules(ContainerInterface $container, array $modules = [])
    {
        foreach ($this->vendor->getVendors() as $vendor) {
            $vendorPath = $this->applicationPath->getModulesDir() . $vendor . '/';
            $vendorModules = count($modules) > 0 ? $modules : Filesystem::scandir($vendorPath);

            foreach ($vendorModules as $module) {
                $module = strtolower($module);

                if (isset($this->results[$module])) {
                    continue;
                }

                $modulePath = $vendorPath . ucfirst($module) . '/';
                $moduleConfigPath = $modulePath . 'Resources/config/module.xml';

                if (is_dir($modulePath) && is_file($moduleConfigPath)) {
                    $dependencies = $this->getModuleDependencies($moduleConfigPath);

                    if (count($dependencies) > 0) {
                        $this->updateModules($container, $dependencies);
                    }

                    $this->results[$module] = $this->updateModule($container, $module);
                }
            }
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param ContainerInterface $container
     * @param string $module
     * @return int
     */
    public function updateModule(ContainerInterface $container, $module)
    {
        $result = false;

        $serviceIdSchema = $module . '.installer.schema';
        $serviceIdMigration = $module . '.installer.migration';
        if ($container->has($serviceIdSchema) === true &&
            $container->has($serviceIdMigration) === true
        ) {
            /** @var SchemaInterface $moduleSchema */
            $moduleSchema = $container->get($serviceIdSchema);
            /** @var MigrationInterface $moduleMigration */
            $moduleMigration = $container->get($serviceIdMigration);
            if ($this->modules->isInstalled($module) || count($moduleMigration->renameModule()) > 0) {
                $result = $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
            }
        }

        return $result;
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
