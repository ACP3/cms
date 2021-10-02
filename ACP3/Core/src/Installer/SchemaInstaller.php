<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Database\Connection;
use ACP3\Core\Modules\InstallerInterface;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\Settings\Repository\SettingsAwareRepositoryInterface;
use Psr\Log\LoggerInterface;

class SchemaInstaller extends SchemaHelper implements InstallerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \ACP3\Core\Settings\Repository\SettingsAwareRepositoryInterface
     */
    private $systemSettingsRepository;

    public function __construct(
        LoggerInterface $logger,
        Connection $db,
        ModuleAwareRepositoryInterface $systemModuleRepository,
        SettingsAwareRepositoryInterface $systemSettingsRepository
    ) {
        parent::__construct($db, $systemModuleRepository);

        $this->logger = $logger;
        $this->systemSettingsRepository = $systemSettingsRepository;
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

        $this->executeSqlQueries($schema->createTables(), $schema->getModuleName());

        return $this->addToModulesTable($schema->getModuleName())
            && $this->installSettings($schema->getModuleName(), $schema->settings());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function moduleNeedsInstallation(SchemaInterface $schema): bool
    {
        return !$this->getModuleAwareRepository()->moduleExists($schema->getModuleName());
    }

    /**
     * Adds a module to the modules SQL-table.
     */
    private function addToModulesTable(string $moduleName): bool
    {
        $insertValues = [
            'name' => $moduleName,
        ];

        return (bool) $this->getModuleAwareRepository()->insert($insertValues);
    }

    /**
     * Installs all module settings.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    private function installSettings(string $moduleName, array $settings): bool
    {
        if (\count($settings) > 0) {
            $this->getDb()->getConnection()->beginTransaction();

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
                $this->getDb()->getConnection()->commit();
            } catch (\Exception $e) {
                $this->getDb()->getConnection()->rollBack();

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
    private function removeFromModulesTable(string $moduleName): bool
    {
        return $this->getModuleAwareRepository()->delete($this->getModuleId($moduleName)) !== false;
    }
}
