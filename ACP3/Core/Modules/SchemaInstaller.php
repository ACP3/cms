<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Modules\ACP3\System;

/**
 * Class SchemaInstaller
 * @package ACP3\Core\Modules
 */
class SchemaInstaller extends SchemaHelper implements InstallerInterface
{
    /**
     * Installs a module
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function install(SchemaInterface $schema)
    {
        return
            $this->executeSqlQueries($schema->createTables(), $schema->getModuleName()) &&
            $this->addToModulesTable($schema->getModuleName(), $schema->getSchemaVersion()) &&
            $this->installSettings($schema->getModuleName(), $schema->settings());
    }

    /**
     * Adds a module to the modules SQL-table
     *
     * @param string $moduleName
     * @param int    $schemaVersion
     *
     * @return bool
     */
    protected function addToModulesTable($moduleName, $schemaVersion)
    {
        // Modul in die Modules-SQL-Tabelle eintragen
        $insertValues = [
            'id' => '',
            'name' => $moduleName,
            'version' => $schemaVersion,
            'active' => 1
        ];

        return $this->systemModuleRepository->insert($insertValues) !== false;
    }

    /**
     * Installs all module settings
     *
     * @param string $moduleName
     * @param array  $settings
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function installSettings($moduleName, array $settings)
    {
        if (count($settings) > 0) {
            $this->db->getConnection()->beginTransaction();
            try {
                $moduleId = $this->getModuleId($moduleName);
                foreach ($settings as $key => $value) {
                    $insertValues = [
                        'id' => '',
                        'module_id' => $moduleId,
                        'name' => $key,
                        'value' => $value
                    ];
                    $this->systemSettingsRepository->insert($insertValues);
                }
                $this->db->getConnection()->commit();
            } catch (\Exception $e) {
                $this->db->getConnection()->rollback();

                $this->container->get('core.logger')->warning('installer', $e);
                return false;
            }
        }
        return true;
    }

    /**
     * Method for uninstalling a module
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function uninstall(SchemaInterface $schema)
    {
        return $this->executeSqlQueries($schema->removeTables(), $schema->getModuleName()) &&
        $this->removeFromModulesTable($schema->getModuleName());
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle
     *
     * @param string $moduleName
     *
     * @return bool
     */
    protected function removeFromModulesTable($moduleName)
    {
        return $this->systemModuleRepository->delete((int)$this->getModuleId($moduleName)) !== false;
    }
}
