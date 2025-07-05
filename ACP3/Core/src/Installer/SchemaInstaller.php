<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Modules\InstallerInterface;
use ACP3\Core\Settings\Repository\SettingsAwareRepositoryInterface;

class SchemaInstaller implements InstallerInterface
{
    public function __construct(
        private readonly SchemaHelper $schemaHelper,
        private readonly SettingsAwareRepositoryInterface $systemSettingsRepository,
    ) {
    }

    /**
     * Installs a module.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function install(SchemaInterface $schema): bool
    {
        if (!$this->moduleNeedsInstallation($schema)) {
            return true;
        }

        $this->schemaHelper->executeSqlQueries($schema->createTables(), $schema->getModuleName());

        return $this->addToModulesTable($schema->getModuleName())
            && $this->installSettings($schema->getModuleName(), $schema->settings());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function moduleNeedsInstallation(SchemaInterface $schema): bool
    {
        return !$this->schemaHelper->getModuleAwareRepository()->moduleExists($schema->getModuleName());
    }

    /**
     * Adds a module to the modules SQL-table.
     */
    private function addToModulesTable(string $moduleName): bool
    {
        $insertValues = [
            'name' => $moduleName,
        ];

        return (bool) $this->schemaHelper->getModuleAwareRepository()->insert($insertValues);
    }

    /**
     * Installs all module settings.
     *
     * @param array<string, string|int|bool|float|null> $settings
     */
    private function installSettings(string $moduleName, array $settings): bool
    {
        if (\count($settings) > 0) {
            $moduleId = $this->schemaHelper->getModuleId($moduleName);
            foreach ($settings as $key => $value) {
                $insertValues = [
                    'module_id' => $moduleId,
                    'name' => $key,
                    'value' => $value,
                ];
                $this->systemSettingsRepository->insert($insertValues);
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
            $this->schemaHelper->executeSqlQueries($schema->removeTables(), $schema->getModuleName());
        } catch (\Throwable) {
            return false;
        }

        return $this->removeFromModulesTable($schema->getModuleName());
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle.
     */
    private function removeFromModulesTable(string $moduleName): bool
    {
        return $this->schemaHelper->getModuleAwareRepository()->delete($this->schemaHelper->getModuleId($moduleName)) > 0;
    }
}
