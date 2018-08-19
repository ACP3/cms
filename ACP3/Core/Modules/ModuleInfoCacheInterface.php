<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

interface ModuleInfoCacheInterface
{
    /**
     * @return array
     */
    public function getModulesInfoCache(): array;

    /**
     * Saves the modules info cache.
     */
    public function saveModulesInfoCache(): void;
}
