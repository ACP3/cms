<?php
namespace ACP3\Modules\System;

use ACP3\Core;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class Helpers
 * @package ACP3\Modules\System
 */
class Helpers
{
    /**
     * @var Core\DB
     */
    protected $db;
    /**
     * @var Core\Modules
     */
    protected $modules;

    /**
     * @param Core\DB $db
     * @param Core\Modules $modules
     */
    public function __construct(
        Core\DB $db,
        Core\Modules $modules
    ) {
        $this->db = $db;
        $this->modules = $modules;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
     *
     * @param Core\Modules\AbstractInstaller $moduleInstaller
     * @return array
     */
    public function checkInstallDependencies(Core\Modules\AbstractInstaller $moduleInstaller)
    {
        $dependencies = $moduleInstaller->getDependencies();
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
     * @param                                                  $moduleToBeUninstalled
     * @param \Symfony\Component\DependencyInjection\Container $container
     *
     * @return array
     */
    public function checkUninstallDependencies($moduleToBeUninstalled, Container $container)
    {
        $modules = $this->modules->getInstalledModules();
        $moduleDependencies = [];

        foreach ($modules as $module) {
            $moduleName = strtolower($module['dir']);
            if ($moduleName !== $moduleToBeUninstalled) {
                $service = $moduleName . '.installer';

                if ($container->has($service) === true) {
                    $deps = $container->get($moduleName . '.installer')->getDependencies();
                    if (!empty($deps) && in_array($moduleToBeUninstalled, $deps) === true) {
                        $moduleDependencies[] = $module['name'];
                    }
                }
            }
        }
        return $moduleDependencies;
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
        $loader->load(ACP3_DIR . 'config/services.yml');
        $loader->load(CLASSES_DIR . 'View/Renderer/Smarty/services.yml');

        // Try to get all available services
        if ($allModules === true) {
            $modules = $this->modules->getAllModules();
        } else {
            $modules = $this->modules->getInstalledModules();
        }
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module['dir'] . '/config/services.yml';
            if (is_file($path)) {
                $loader->load($path);
            }
        }

        $container->compile();

        return $container;
    }

    /**
     * @param array $tables
     * @param $exportType
     * @param $withDropTables
     * @return string
     */
    public function exportDatabase(array $tables, $exportType, $withDropTables)
    {
        $structure = $data = '';
        foreach ($tables as $table) {
            // Struktur ausgeben
            if ($exportType === 'complete' || $exportType === 'structure') {
                $result = $this->db->getConnection()->fetchAssoc('SHOW CREATE TABLE ' . $table);
                if (!empty($result)) {
                    $structure .= $withDropTables == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
                    $structure .= $result['Create Table'] . ';' . "\n\n";
                }
            }

            // Datensätze ausgeben
            if ($exportType === 'complete' || $exportType === 'data') {
                $resultSets = $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . substr($table, strlen($this->db->getPrefix())));
                if (count($resultSets) > 0) {
                    $fields = '';
                    // Felder der jeweiligen Tabelle auslesen
                    foreach (array_keys($resultSets[0]) as $field) {
                        $fields .= '`' . $field . '`, ';
                    }

                    // Datensätze auslesen
                    foreach ($resultSets as $row) {
                        $values = '';
                        foreach ($row as $value) {
                            $values .= '\'' . $value . '\', ';
                        }
                        $data .= 'INSERT INTO `' . $table . '` (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ');' . "\n";
                    }
                }
            }
        }

        return $structure . $data;
    }
}
