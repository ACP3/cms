<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;


use ACP3\Core\Filesystem;
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
     * @param array $modules
     * @throws \Exception
     */
    public function installModules(ContainerInterface $container, array $modules = [])
    {
        foreach ($this->vendor->getVendors() as $vendor) {
            $vendorPath = $this->applicationPath->getModulesDir() . $vendor . '/';
            $modules = count($modules) > 0 ? $modules : Filesystem::scandir($vendorPath);

            foreach ($modules as $module) {
                $modulePath = $vendorPath . ucfirst($module) . '/';
                $moduleConfigPath = $modulePath . 'Resources/config/module.xml';

                if (is_dir($modulePath) && is_file($moduleConfigPath)) {
                    $dependencies = $this->getModuleDependencies($moduleConfigPath);

                    if (count($dependencies) > 0) {
                        $this->installModules($container, $dependencies);
                    }

                    if ($this->installHelper->installModule($module, $container) === false) {
                        throw new \Exception("Error while installing module {$module}.");
                    }
                }
            }
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
