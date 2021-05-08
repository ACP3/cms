<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Cache;
use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\XML;
use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;

class ModuleInfoCache implements ModuleInfoCacheInterface
{
    /**
     * @var \ACP3\Core\Cache
     */
    private $cache;
    /**
     * @var \ACP3\Core\XML
     */
    private $xml;
    /**
     * @var ContainerInterface
     */
    private $schemaLocator;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    private $systemModuleRepository;

    public function __construct(
        Cache $cache,
        XML $xml,
        ContainerInterface $schemaLocator,
        ModuleAwareRepositoryInterface $systemModuleRepository
    ) {
        $this->cache = $cache;
        $this->xml = $xml;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->schemaLocator = $schemaLocator;
    }

    private function getCacheKey(): string
    {
        return 'modules_info';
    }

    /**
     * @throws \JsonException
     */
    public function getModulesInfoCache(): array
    {
        if ($this->cache->contains($this->getCacheKey()) === false) {
            $this->saveModulesInfoCache();
        }

        return $this->cache->fetch($this->getCacheKey());
    }

    /**
     * Saves the modules info cache.
     *
     * @throws \JsonException
     */
    public function saveModulesInfoCache(): void
    {
        $this->cache->save($this->getCacheKey(), $this->fetchAllModulesInfo());
    }

    /**
     * @throws \JsonException
     */
    private function fetchAllModulesInfo(): array
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
    private function fetchModuleInfo(ComponentDataDto $moduleCoreData): array
    {
        $path = $moduleCoreData->getPath() . '/Resources/config/module.xml';
        if (is_file($path) === false) {
            return [];
        }

        $moduleInfo = $this->xml->parseXmlFile($path, 'info');

        if (empty($moduleInfo) === true) {
            return [];
        }

        $moduleInfoDb = $this->systemModuleRepository->getInfoByModuleName($moduleCoreData->getName());

        $composerData = json_decode(file_get_contents($moduleCoreData->getPath() . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

        $needsInstallation = $this->schemaLocator->has($moduleCoreData->getName());

        return [
            'id' => $moduleInfoDb['id'] ?? 0,
            'dir' => $moduleCoreData->getPath(),
            'installed' => (!empty($moduleInfoDb)) || !$needsInstallation,
            'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1) || !$needsInstallation,
            'schema_version' => !empty($moduleInfoDb) ? (int) $moduleInfoDb['version'] : 0,
            'author' => $moduleInfo['author'],
            'version' => $moduleInfo['version'] ?? InstalledVersions::getPrettyVersion($composerData['name']) ?: InstalledVersions::getRootPackage()['pretty_version'],
            'name' => $moduleCoreData->getName(),
            'description' => $composerData['description'],
            'protected' => isset($moduleInfo['protected']),
            'installable' => $needsInstallation,
            'dependencies' => $moduleCoreData->getDependencies(),
        ];
    }
}
