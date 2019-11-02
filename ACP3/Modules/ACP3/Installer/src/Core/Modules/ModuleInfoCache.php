<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Modules;

use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Modules\ModuleInfoCacheInterface;
use ACP3\Core\XML;

class ModuleInfoCache implements ModuleInfoCacheInterface
{
    /**
     * @var \ACP3\Core\XML
     */
    private $xml;

    /**
     * @var array
     */
    private $moduleInfoCache = [];

    public function __construct(XML $xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return array
     */
    public function getModulesInfoCache(): array
    {
        if (empty($this->moduleInfoCache)) {
            $this->saveModulesInfoCache();
        }

        return $this->moduleInfoCache;
    }

    /**
     * Saves the modules info cache.
     */
    public function saveModulesInfoCache(): void
    {
        $this->moduleInfoCache = $this->fetchModules();
    }

    /**
     * @return array
     */
    protected function fetchModules(): array
    {
        $infos = [];

        foreach (\ACP3\Core\Component\ComponentRegistry::all() as $module) {
            $moduleInfo = $this->fetchModuleInfo($module);

            if (!empty($moduleInfo)) {
                $infos[$module->getName()] = $moduleInfo;
            }
        }

        return $infos;
    }

    /**
     * @param \ACP3\Core\Component\Dto\ComponentDataDto $module
     *
     * @return array
     */
    protected function fetchModuleInfo(ComponentDataDto $module): array
    {
        $path = $module->getPath() . '/Resources/config/module.xml';
        if (\is_file($path) === false) {
            return [];
        }

        $moduleInfo = $this->xml->parseXmlFile($path, 'info');

        if (empty($moduleInfo) === true) {
            return [];
        }

        return [
            'id' => 0,
            'dir' => $module->getPath(),
            'installed' => false,
            'active' => false,
            'schema_version' => 0,
            'author' => $moduleInfo['author'],
            'version' => $moduleInfo['version'],
            'name' => $module->getName(),
            'categories' => isset($moduleInfo['categories']),
            'protected' => isset($moduleInfo['protected']),
            'installable' => !isset($moduleInfo['no_install']),
            'dependencies' => $module->getDependencies(),
        ];
    }
}
