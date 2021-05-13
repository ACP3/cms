<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Cache;
use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;

class ModuleInfoCache implements ModuleInfoCacheInterface
{
    /**
     * @var \ACP3\Core\Cache
     */
    private $cache;
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
        ContainerInterface $schemaLocator,
        ModuleAwareRepositoryInterface $systemModuleRepository
    ) {
        $this->cache = $cache;
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

        $modules = ComponentRegistry::excludeByType(ComponentRegistry::all(), [ComponentTypeEnum::THEME]);

        $moduleNames = [];
        foreach ($modules as $module) {
            $moduleNames[] = $module->getName();
        }

        $moduleInfoDb = $this->systemModuleRepository->getInfoByModuleNameList($moduleNames);

        foreach (ComponentRegistry::excludeByType(ComponentRegistry::all(), [ComponentTypeEnum::THEME]) as $module) {
            $moduleInfo = $this->fetchModuleInfo($module, $moduleInfoDb[$module->getName()] ?? []);

            if (!empty($moduleInfo)) {
                $infos[$module->getName()] = $moduleInfo;
            }
        }

        return $infos;
    }

    /**
     * @throws \JsonException
     */
    private function fetchModuleInfo(ComponentDataDto $moduleCoreData, array $moduleInfoDb): array
    {
        $composerData = json_decode(file_get_contents($moduleCoreData->getPath() . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

        $needsInstallation = $this->schemaLocator->has($moduleCoreData->getName());

        return [
            'id' => $moduleInfoDb['id'] ?? 0,
            'composer_package_name' => $composerData['name'],
            'dir' => $moduleCoreData->getPath(),
            'installed' => (!empty($moduleInfoDb)) || !$needsInstallation,
            // @deprecated since version 5.18.0. To be removed with version 6.0.0.
            'active' => (!empty($moduleInfoDb)) || !$needsInstallation,
            'schema_version' => !empty($moduleInfoDb) ? (int) $moduleInfoDb['version'] : 0,
            'author' => $this->getAuthors($composerData),
            'version' => InstalledVersions::getPrettyVersion($composerData['name']) ?: InstalledVersions::getRootPackage()['pretty_version'],
            'name' => $moduleCoreData->getName(),
            'description' => $composerData['description'],
            'installable' => $needsInstallation,
            'dependencies' => $moduleCoreData->getDependencies(),
        ];
    }

    private function getAuthors(array $composerData): string
    {
        $authors = [];

        foreach ($composerData['authors'] ?? [] as $author) {
            $authors[] = $author['name'];
        }

        return implode(', ', $authors);
    }
}
