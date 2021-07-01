<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Cache;

class CachingModuleInfo implements ModuleInfoInterface
{
    private const CACHE_ID_MODULES_INFO = 'modules_info';

    /**
     * @var Cache
     */
    private $systemCache;
    /**
     * @var ModuleInfo
     */
    private $moduleInfo;

    public function __construct(Cache $modulesCache, ModuleInfo $moduleInfo)
    {
        $this->systemCache = $modulesCache;
        $this->moduleInfo = $moduleInfo;
    }

    public function getModulesInfo(): array
    {
        if (!$this->systemCache->contains(self::CACHE_ID_MODULES_INFO)) {
            $this->systemCache->save(self::CACHE_ID_MODULES_INFO, $this->moduleInfo->getModulesInfo());
        }

        return $this->systemCache->fetch(self::CACHE_ID_MODULES_INFO);
    }
}
