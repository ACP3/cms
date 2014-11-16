<?php

/**
 * System
 *
 * @author     Tino Goratsch
 * @package    ACP3
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
    )
    {
        $this->db = $db;
        $this->modules = $modules;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
     *
     * @param string $module
     *
     * @return array
     */
    public function checkInstallDependencies($module)
    {
        $module = strtolower($module);
        $deps = Core\Modules\AbstractInstaller::getDependencies($module);
        $modulesToEnable = [];
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
     *
     * @return array
     */
    public function checkUninstallDependencies($module)
    {
        $module = strtolower($module);
        $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
        $modulesToUninstall = [];
        foreach ($modules as $row) {
            $row = strtolower($row);
            if ($row !== $module) {
                $deps = Core\Modules\AbstractInstaller::getDependencies($row); // Modulabhängigkeiten
                if (!empty($deps) && $this->modules->isInstalled($row) === true && in_array($module, $deps) === true) {
                    $modulesToUninstall[] = ucfirst($row);
                }
            }
        }
        return $modulesToUninstall;
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