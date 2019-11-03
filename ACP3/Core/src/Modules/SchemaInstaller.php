<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\SchemaInterface;

class SchemaInstaller extends SchemaHelper implements InstallerInterface
{
    /**
     * Installs a module.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function install(SchemaInterface $schema): bool
    {
        if (!$this->moduleNeedsInstallation($schema)) {
            return true;
        }

        try {
            $this->executeSqlQueries($schema->createTables(), $schema->getModuleName());
        } catch (\Throwable $e) {
            return false;
        }

        return $this->addToModulesTable($schema->getModuleName(), $schema->getSchemaVersion())
            && $this->installSettings($schema->getModuleName(), $schema->settings());
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function moduleNeedsInstallation(SchemaInterface $schema): bool
    {
        $modulesTableExists = $this->db->fetchColumn("SHOW TABLES LIKE '{$this->systemModuleRepository->getTableName()}'");

        return !$modulesTableExists || !$this->systemModuleRepository->moduleExists($schema->getModuleName());
    }

    /**
     * Adds a module to the modules SQL-table.
     */
    protected function addToModulesTable(string $moduleName, int $schemaVersion): bool
    {
        $insertValues = [
            'name' => $moduleName,
            'version' => $schemaVersion,
            'active' => 1,
        ];

        return $this->systemModuleRepository->insert($insertValues) !== false;
    }

    /**
     * Installs all module settings.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function installSettings(string $moduleName, array $settings): bool
    {
        if (\count($settings) > 0) {
            $this->db->getConnection()->beginTransaction();

            try {
                $moduleId = $this->getModuleId($moduleName);
                foreach ($settings as $key => $value) {
                    $insertValues = [
                        'module_id' => $moduleId,
                        'name' => $key,
                        'value' => $value,
                    ];
                    $this->systemSettingsRepository->insert($insertValues);
                }
                $this->db->getConnection()->commit();
            } catch (\Exception $e) {
                $this->db->getConnection()->rollBack();

                $this->logger->warning($e);

                return false;
            }
        }

        return true;
    }

    /**
     * Method for uninstalling a module.
     */
    public function uninstall(SchemaInterface $schema): bool
    {
        try {
            $this->executeSqlQueries($schema->removeTables(), $schema->getModuleName());
        } catch (\Throwable $e) {
            return false;
        }

        return $this->removeFromModulesTable($schema->getModuleName());
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle.
     */
    protected function removeFromModulesTable(string $moduleName): bool
    {
        return $this->systemModuleRepository->delete($this->getModuleId($moduleName)) !== false;
    }
}
