<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Modules\ModuleDependenciesTrait;
use ACP3\Core\Modules\Vendor;
use ACP3\Core\XML;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModuleInstaller
{
    use ModuleDependenciesTrait;

    /**
     * @var ApplicationPath
     */
    protected $applicationPath;
    /**
     * @var Vendor
     */
    protected $vendor;
    /**
     * @var XML
     */
    protected $xml;
    /**
     * @var Install
     */
    protected $installHelper;
    /**
     * @var array
     */
    protected $installedModules = [];

    /**
     * ModuleInstaller constructor.
     * @param ApplicationPath $applicationPath
     * @param Vendor $vendor
     * @param XML $xml
     * @param Install $installHelper
     */
    public function __construct(
        ApplicationPath $applicationPath,
        Vendor $vendor,
        XML $xml,
        Install $installHelper
    ) {
        $this->applicationPath = $applicationPath;
        $this->vendor = $vendor;
        $this->xml = $xml;
        $this->installHelper = $installHelper;
    }

    /**
     * @param ContainerInterface $container
     * @param SchemaInterface[] $schemas
     * @return array
     * @throws \Exception
     */
    public function installModules(ContainerInterface $container, array $schemas)
    {
        foreach ($schemas as $schema) {
            foreach ($this->vendor->getVendors() as $vendor) {
                $vendorPath = $this->applicationPath->getModulesDir() . $vendor . '/';
                $module = $schema->getModuleName();

                $moduleConfigPath = $vendorPath . ucfirst($module) . '/Resources/config/module.xml';

                if ($this->isValidModule($moduleConfigPath)) {
                    $dependencies = $this->getModuleDependencies($moduleConfigPath);

                    if (count($dependencies) > 0) {
                        $this->installModules($container, $this->collectDependentSchemas($container, $dependencies));
                    }

                    if ($this->installHelper->installModule($schema, $container) === false) {
                        throw new \Exception("Error while installing module {$module}.");
                    }

                    $this->installedModules[$module] = true;

                    break;
                }
            }
        }

        return $this->installedModules;
    }

    /**
     * @param string $moduleConfigPath
     * @return bool
     */
    private function isValidModule($moduleConfigPath)
    {
        if (is_file($moduleConfigPath)) {
            $config = $this->xml->parseXmlFile($moduleConfigPath, '/module/info');

            return !isset($config['no_install']);
        }

        return false;
    }

    /**
     * @param ContainerInterface $container
     * @param array $modules
     * @return SchemaInterface[]
     */
    private function collectDependentSchemas(ContainerInterface $container, array $modules)
    {
        /** @var SchemaRegistrar $schemaRegistrar */
        $schemaRegistrar = $container->get('core.installer.schema_registrar');

        $schemas = [];
        foreach ($modules as $module) {
            if ($schemaRegistrar->has($module)) {
                $schemas[] = $schemaRegistrar->get($module);
            }
        }

        return $schemas;
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
