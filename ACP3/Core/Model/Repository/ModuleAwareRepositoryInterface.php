<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

interface ModuleAwareRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleId(string $moduleName): int;

    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleSchemaVersion(string $moduleName): int;

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    public function moduleExists(string $moduleName): bool;

    /**
     * @param string $moduleName
     *
     * @return array
     */
    public function getInfoByModuleName(string $moduleName): array;

    /**
     * @param int $moduleId
     *
     * @return string
     */
    public function getModuleNameById(int $moduleId): string;
}
