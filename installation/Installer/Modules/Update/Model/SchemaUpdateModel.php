<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Update\Model;

use ACP3\Core\Filesystem;
use ACP3\Core\Installer\MigrationRegistrar;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Modules;
use ACP3\Core\XML;
use ACP3\Installer\Core\Environment\ApplicationPath;

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
     * @var SchemaRegistrar
     */
    private $schemaRegistrar;
    /**
     * @var MigrationRegistrar
     */
    private $migrationRegistrar;
    /**
     * @var array
     */
    protected $results = [];

    /**
     * ModuleUpdateModel constructor.
     * @param ApplicationPath $applicationPath
     * @param XML $xml
     * @param SchemaRegistrar $schemaRegistrar
     * @param MigrationRegistrar $migrationRegistrar
     * @param Modules\Vendor $vendor
     * @param Modules $modules
     * @param Modules\SchemaUpdater $schemaUpdater
     */
    public function __construct(
        ApplicationPath $applicationPath,
        XML $xml,
        SchemaRegistrar $schemaRegistrar,
        MigrationRegistrar $migrationRegistrar,
        Modules\Vendor $vendor,
        Modules $modules,
        Modules\SchemaUpdater $schemaUpdater)
    {
        $this->applicationPath = $applicationPath;
        $this->xml = $xml;
        $this->vendor = $vendor;
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->migrationRegistrar = $migrationRegistrar;
    }

    /**
     * @param array $modules
     * @return array
     */
    public function updateModules(array $modules = [])
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
                        $this->updateModules($dependencies);
                    }

                    $this->results[$module] = $this->updateModule($module);
                }
            }
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string $module
     * @return int
     */
    public function updateModule($module)
    {
        $result = false;

        $serviceIdMigration = $module . '.installer.migration';
        if ($this->schemaRegistrar->has($module) === true &&
            $this->migrationRegistrar->has($serviceIdMigration) === true
        ) {
            $moduleSchema = $this->schemaRegistrar->get($module);
            $moduleMigration = $this->migrationRegistrar->get($serviceIdMigration);
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
