<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

interface ModuleAwareRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleId($moduleName);

    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleSchemaVersion($moduleName);

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    public function moduleExists($moduleName);

    /**
     * @param string $moduleName
     *
     * @return array
     */
    public function getInfoByModuleName($moduleName);

    /**
     * @param int $moduleId
     *
     * @return string
     */
    public function getModuleNameById($moduleId);
}
