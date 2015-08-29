<?php
namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\System\Helper
 */
class Installer
{
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Modules\SchemaInstaller
     */
    protected $schemaInstaller;
    /**
     * @var \ACP3\Core\Modules\Vendors
     */
    protected $vendors;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;

    /**
     * @param \ACP3\Core\Modules                 $modules
     * @param \ACP3\Core\Modules\Vendors         $vendors
     * @param \ACP3\Core\Modules\SchemaInstaller $schemaInstaller
     * @param \ACP3\Core\XML                     $xml
     */
    public function __construct(
        Core\Modules $modules,
        Core\Modules\Vendors $vendors,
        Core\Modules\SchemaInstaller $schemaInstaller,
        Core\XML $xml
    )
    {
        $this->modules = $modules;
        $this->vendors = $vendors;
        $this->schemaInstaller = $schemaInstaller;
        $this->xml = $xml;
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
                $service = $moduleName . '.installer.schema';

                if ($container->has($service) === true) {
                    $deps = $this->getDependencies($moduleToBeUninstalled);
                    if (!empty($deps) && in_array($moduleToBeUninstalled, $deps) === true) {
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
            $path = MODULES_DIR . ucfirst($moduleName) . '/config/module.xml';
            if (is_file($path) === true) {
                return array_values($this->xml->parseXmlFile($path, '/module/info/dependencies'));
            }
        }

        return [];
    }

    /**
     * @param bool $allModules
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function updateServiceContainer($allModules = false)
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load(CLASSES_DIR . 'config/services.yml');
        $loader->load(CLASSES_DIR . 'View/Renderer/Smarty/config/services.yml');

        // Try to get all available services
        $modules = ($allModules === true) ? $this->modules->getAllModules() : $this->modules->getInstalledModules();
        $vendors = $this->vendors->getVendors();

        foreach ($modules as $module) {
            foreach ($vendors as $vendor) {
                $path = MODULES_DIR . $vendor . '/' . $module['dir'] . '/config/services.yml';
                if (is_file($path)) {
                    $loader->load($path);
                }
            }
        }

        $container->compile();

        return $container;
    }
}
