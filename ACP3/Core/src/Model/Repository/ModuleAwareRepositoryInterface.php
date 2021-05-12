<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

interface ModuleAwareRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the ID of the given module.
     */
    public function getModuleId(string $moduleName): int;

    /**
     * Returns the current DB schema version of the given module.
     */
    public function getModuleSchemaVersion(string $moduleName): int;

    /**
     * Checks, whether the given is registered within the database or not.
     */
    public function moduleExists(string $moduleName): bool;

    /**
     * Checks, whether the bare minimum of the required database tables exist.
     * This method is only relevant when newly installing the ACP3.
     */
    public function coreTablesExist(): bool;

    /**
     * Returns the basic information about the given module.
     * When successful, it returns an array with the module-ID and its DB schema version.
     */
    public function getInfoByModuleName(string $moduleName): array;

    /**
     * Returns the basic information about the given modules.
     * When successful, it returns a map of the requested modules with their corresponding module-ID and its DB schema version.
     *
     * @param string[] $moduleNames
     *
     * @return array<string, array<string, mixed>>
     */
    public function getInfoByModuleNameList(array $moduleNames): array;

    /**
     * Returns the internal module name by the given module-ID.
     * It is effectively the reserve of ::getModuleId().
     */
    public function getModuleNameById(int $moduleId): string;
}
