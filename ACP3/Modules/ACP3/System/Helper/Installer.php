<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\System\Helper
 */
class Installer
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Modules\SchemaInstaller
     */
    protected $schemaInstaller;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var string
     */
    protected $environment;

    /**
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules                     $modules
     * @param \ACP3\Core\Modules\Vendor              $vendors
     * @param \ACP3\Core\Modules\SchemaInstaller     $schemaInstaller
     * @param \ACP3\Core\XML                         $xml
     * @param string                                 $environment
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Core\Modules $modules,
        Core\Modules\Vendor $vendors,
        Core\Modules\SchemaInstaller $schemaInstaller,
        Core\XML $xml,
        $environment
    ) {
        $this->appPath = $appPath;
        $this->modules = $modules;
        $this->vendors = $vendors;
        $this->schemaInstaller = $schemaInstaller;
        $this->xml = $xml;
        $this->environment = $environment;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return array
     */
    public function checkInstallDependencies(Core\Modules\Installer\SchemaInterface $schema)
    {
        $dependencies = $this->getDependencies($schema->getModuleName());
        $modulesToEnable = [];
        if (!empty($dependencies)) {
            foreach ($dependencies as $dependency) {
                if ($this->modules->isActive($dependency) === false) {
                    $moduleInfo = $this->modules->getModuleInfo($dependency);
                    $modulesToEnable[] = $moduleInfo['name'];
                }
            }
        }
        return $modulesToEnable;
    }

    /**
     * @param string                                                    $moduleToBeUninstalled
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return array
     */
    public function checkUninstallDependencies($moduleToBeUninstalled, ContainerInterface $container)
    {
        $modules = $this->modules->getInstalledModules();
        $moduleDependencies = [];

        foreach ($modules as $module) {
            $moduleName = strtolower($module['dir']);
            if ($moduleName !== $moduleToBeUninstalled) {
                $serviceId = $moduleName . '.installer.schema';

                if ($container->has($serviceId) === true) {
                    $dependencies = $this->getDependencies($moduleName);

                    if (in_array($moduleToBeUninstalled, $dependencies) === true) {
                        $moduleDependencies[] = $module['name'];
                    }
                }
            }
        }
        return $moduleDependencies;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück
     *
     * @param string $moduleName
     *
     * @return array
     */
    protected function getDependencies($moduleName)
    {
        if ((bool)preg_match('=/=', $moduleName) === false) {
            foreach ($this->vendors->getVendors() as $vendor) {
                $path = $this->appPath->getModulesDir() . $vendor . '/' . ucfirst($moduleName) . '/Resources/config/module.xml';

                if (is_file($path) === true) {
                    $dependencies = $this->xml->parseXmlFile($path, '/module/info/dependencies');
                    return is_array($dependencies['module']) ? $dependencies['module'] : [$dependencies['module']];
                }
            }
        }

        return [];
    }

    /**
     * @param Core\Http\RequestInterface $request
     * @param bool $allModules
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function updateServiceContainer(Core\Http\RequestInterface $request, $allModules = false)
    {
        return Core\DependencyInjection\ServiceContainerBuilder::create(
            $this->appPath,
            $request->getSymfonyRequest(),
            $this->environment,
            $allModules
        );
    }
}
