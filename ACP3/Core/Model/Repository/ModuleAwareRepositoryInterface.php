<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

interface ModuleAwareRepositoryInterface extends WriterRepositoryInterface
{
    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleId(string $moduleName);

    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleSchemaVersion(string $moduleName);

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    public function moduleExists(string $moduleName);

    /**
     * @param string $moduleName
     *
     * @return array
     */
    public function getInfoByModuleName(string $moduleName);

    /**
     * @param int $moduleId
     *
     * @return string
     */
    public function getModuleNameById(int $moduleId);
}
