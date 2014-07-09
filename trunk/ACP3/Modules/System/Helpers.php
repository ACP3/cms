<?php

/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\System;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\System
 */
class Helpers
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Core\Modules
     */
    protected $modules;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Modules $modules)
    {
        $this->db = $db;
        $this->modules = $modules;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
     *
     * @param string $module
     * @return array
     */
    public function checkInstallDependencies($module)
    {
        $module = strtolower($module);
        $deps = Core\Modules\AbstractInstaller::getDependencies($module);
        $modulesToEnable = array();
        if (!empty($deps)) {
            foreach ($deps as $dep) {
                if ($this->modules->isActive($dep) === false) {
                    $modulesToEnable[] = ucfirst($dep);
                }
            }
        }
        return $modulesToEnable;
    }

    /**
     * Überprüft die Modulabhängigkeiten vor dem Deinstallieren eines Moduls
     *
     * @param string $module
     * @return array
     */
    public function checkUninstallDependencies($module)
    {
        $module = strtolower($module);
        $modules = scandir(MODULES_DIR);
        $modulesToUninstall = array();
        foreach ($modules as $row) {
            $row = strtolower($row);
            if ($row !== '.' && $row !== '..' && $row !== $module) {
                $deps = Core\Modules\AbstractInstaller::getDependencies($row); // Modulabhängigkeiten
                if (!empty($deps) && $this->modules->isInstalled($row) === true && in_array($module, $deps) === true) {
                    $modulesToUninstall[] = ucfirst($row);
                }
            }
        }
        return $modulesToUninstall;
    }

    public function exportDatabase(array $tables, $exportType, $withDropTables)
    {
        $structure = $data = '';
        foreach ($tables as $table) {
            // Struktur ausgeben
            if ($exportType === 'complete' || $exportType === 'structure') {
                $result = $this->db->fetchAssoc('SHOW CREATE TABLE ' . $table);
                if (!empty($result)) {
                    $structure .= $withDropTables == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
                    $structure .= $result['Create Table'] . ';' . "\n\n";
                }
            }

            // Datensätze ausgeben
            if ($exportType === 'complete' || $exportType === 'data') {
                $resultSets = $this->db->fetchAll('SELECT * FROM ' . DB_PRE . substr($table, strlen(CONFIG_DB_PRE)));
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