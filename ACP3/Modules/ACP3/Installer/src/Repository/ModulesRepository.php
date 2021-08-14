<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Repository;

use ACP3\Core\Database\Connection;

class ModulesRepository extends \ACP3\Modules\ACP3\System\Repository\ModulesRepository
{
    /**
     * @var bool
     */
    private $installationIsInProgress;

    public function __construct(Connection $db, bool $installationIsInProgress)
    {
        parent::__construct($db);

        $this->installationIsInProgress = $installationIsInProgress;
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleId(string $moduleName): int
    {
        return !$this->installationIsInProgress ? 0 : parent::getModuleId($moduleName);
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleSchemaVersion(string $moduleName): int
    {
        return !$this->installationIsInProgress ? 0 : parent::getModuleSchemaVersion($moduleName);
    }

    /**
     * {@inheritDoc}
     */
    public function moduleExists(string $moduleName): bool
    {
        if ($this->installationIsInProgress) {
            return false;
        }

        return parent::moduleExists($moduleName);
    }

    /**
     * {@inheritDoc}
     */
    public function coreTablesExist(): bool
    {
        if ($this->installationIsInProgress) {
            return false;
        }

        return parent::coreTablesExist();
    }

    /**
     * {@inheritDoc}
     */
    public function getInfoByModuleName(string $moduleName): array
    {
        return !$this->installationIsInProgress ? [] : parent::getInfoByModuleName($moduleName);
    }

    /**
     * {@inheritDoc}
     */
    public function getInfoByModuleNameList(array $moduleNames): array
    {
        return !$this->installationIsInProgress ? [] : parent::getInfoByModuleNameList($moduleNames);
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleNameById(int $moduleId): string
    {
        return !$this->installationIsInProgress ? '' : parent::getModuleNameById($moduleId);
    }
}
