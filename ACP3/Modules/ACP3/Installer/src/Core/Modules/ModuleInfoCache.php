<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Modules;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Modules\ModuleInfoCacheInterface;
use ACP3\Core\XML;
use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;

class ModuleInfoCache implements ModuleInfoCacheInterface
{
    /**
     * @var \ACP3\Core\XML
     */
    private $xml;
    /**
     * @var ContainerInterface
     */
    private $schemaLocator;

    /**
     * @var array
     */
    private $moduleInfoCache = [];

    public function __construct(ContainerInterface $schemaLocator, XML $xml)
    {
        $this->xml = $xml;
        $this->schemaLocator = $schemaLocator;
    }

    /**
     * @throws \JsonException
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
     *
     * @throws \JsonException
     */
    public function saveModulesInfoCache(): void
    {
        $this->moduleInfoCache = $this->fetchModules();
    }

    /**
     * @throws \JsonException
     */
    private function fetchModules(): array
    {
        $infos = [];

        foreach (ComponentRegistry::all() as $module) {
            $moduleInfo = $this->fetchModuleInfo($module);

            if (!empty($moduleInfo)) {
                $infos[$module->getName()] = $moduleInfo;
            }
        }

        return $infos;
    }

    /**
     * @throws \JsonException
     */
    private function fetchModuleInfo(ComponentDataDto $module): array
    {
        $path = $module->getPath() . '/Resources/config/module.xml';
        if (is_file($path) === false) {
            return [];
        }

        $moduleInfo = $this->xml->parseXmlFile($path, 'info');

        if (empty($moduleInfo) === true) {
            return [];
        }

        $composerData = json_decode(file_get_contents($module->getPath() . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

        $needsInstallation = $this->schemaLocator->has($module->getName());

        return [
            'id' => 0,
            'dir' => $module->getPath(),
            'installed' => !$needsInstallation,
            'active' => false,
            'schema_version' => 0,
            'author' => $moduleInfo['author'],
            'version' => $moduleInfo['version'] ?? InstalledVersions::getPrettyVersion($composerData['name']) ?: InstalledVersions::getRootPackage()['pretty_version'],
            'name' => $module->getName(),
            'protected' => isset($moduleInfo['protected']),
            'installable' => $needsInstallation,
            'dependencies' => $module->getDependencies(),
        ];
    }
}
