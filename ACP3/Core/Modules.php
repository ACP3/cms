<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Helper\ControllerActionExists;
use ACP3\Core\Modules\ModuleInfoCache;
use ACP3\Core\Modules\Vendor;
use ACP3\Modules\ACP3\System;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Modules
 * @package ACP3\Core
 */
class Modules
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Helper\ControllerActionExists
     */
    protected $controllerActionExists;
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCache
     */
    protected $moduleInfoCache;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var array
     */
    private $modulesInfo = [];
    /**
     * @var array
     */
    private $allModules = [];

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \ACP3\Core\Environment\ApplicationPath                    $appPath
     * @param \ACP3\Core\Modules\Helper\ControllerActionExists          $controllerActionExists
     * @param \ACP3\Core\Modules\ModuleInfoCache                        $moduleInfoCache
     * @param \ACP3\Core\Modules\Vendor                                 $vendors
     */
    public function __construct(
        ContainerInterface $container,
        ApplicationPath $appPath,
        ControllerActionExists $controllerActionExists,
        ModuleInfoCache $moduleInfoCache,
        Vendor $vendors
    )
    {
        $this->container = $container;
        $this->appPath = $appPath;
        $this->controllerActionExists = $controllerActionExists;
        $this->moduleInfoCache = $moduleInfoCache;
        $this->vendors = $vendors;
    }

    /**
     * Returns, whether the given module controller action exists
     *
     * @param string $path
     *
     * @return boolean
     */
    public function controllerActionExists($path)
    {
        return $this->controllerActionExists->controllerActionExists($path);
    }

    /**
     * Returns, whether a module is active or not
     *
     * @param string $module
     *
     * @return boolean
     */
    public function isActive($module)
    {
        $info = $this->getModuleInfo($module);
        return !empty($info) && $info['active'] === true;
    }

    /**
     * Returns the available information about the given module
     *
     * @param string $module
     *
     * @return array
     */
    public function getModuleInfo($module)
    {
        $module = strtolower($module);
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfoCache->getModulesInfoCache();
        }
        return !empty($this->modulesInfo[$module]) ? $this->modulesInfo[$module] : [];
    }

    /**
     * @param string $module
     *
     * @return integer
     */
    public function getModuleId($module)
    {
        $info = $this->getModuleInfo($module);
        return !empty($info) ? $info['id'] : 0;
    }

    /**
     * Checks, whether a modules in currently installed or not
     *
     * @param string $moduleName
     *
     * @return boolean
     */
    public function isInstalled($moduleName)
    {
        $info = $this->getModuleInfo($moduleName);
        return !empty($info) && $info['installed'] === true;
    }

    /**
     * Returns all currently installed AND active modules
     *
     * @return array
     */
    public function getActiveModules()
    {
        $modules = $this->getAllModules();

        foreach ($this->allModules as $key => $values) {
            if ($values['active'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }

    /**
     * Returns all currently installed modules
     *
     * @return array
     */
    public function getInstalledModules()
    {
        $modules = $this->getAllModules();

        foreach ($this->allModules as $key => $values) {
            if ($values['installed'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }

    /**
     * Returns an alphabetically sorted array of all found ACP3 modules
     *
     * @return array
     */
    public function getAllModules()
    {
        if (empty($this->allModules)) {
            foreach ($this->vendors->getVendors() as $vendor) {
                foreach (Filesystem::scandir($this->appPath->getModulesDir() . $vendor . '/') as $module) {
                    $info = $this->getModuleInfo($module);
                    if (!empty($info)) {
                        $this->allModules[$info['name']] = $info;
                    }
                }
            }

            ksort($this->allModules);
        }

        return $this->allModules;
    }
}