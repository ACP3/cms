<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

interface ModuleAwareRepositoryInterface extends RepositoryInterface
{
    public function getModuleId(string $moduleName): ?int;

    public function getModuleSchemaVersion(string $moduleName): ?int;

    public function moduleExists(string $moduleName): bool;

    public function getInfoByModuleName(string $moduleName): array;

    public function getModuleNameById(int $moduleId): ?string;
}
