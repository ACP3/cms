<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;

class ModuleInfo implements ModuleInfoInterface
{
    /**
     * @var ContainerInterface
     */
    private $schemaLocator;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    private $systemModuleRepository;

    public function __construct(
        ContainerInterface $schemaLocator,
        ModuleAwareRepositoryInterface $systemModuleRepository
    ) {
        $this->systemModuleRepository = $systemModuleRepository;
        $this->schemaLocator = $schemaLocator;
    }

    /**
     * @throws \JsonException
     */
    public function getModulesInfo(): array
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
