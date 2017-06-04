<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;
use ACP3\Core\I18n\LocaleInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\XML;
use Composer\Json\JsonFile;

/**
 * Class ModuleInfoCache
 * @package ACP3\Core\Modules
 */
class ModuleInfoCache
{
    use ModuleDependenciesTrait;

    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * ModuleInfoCache constructor.
     * @param Cache $cache
     * @param ApplicationPath $appPath
     * @param TranslatorInterface $translator
     * @param LocaleInterface $locale
     * @param Vendor $vendors
     * @param XML $xml
     * @param ModuleAwareRepositoryInterface $systemModuleRepository
     */
    public function __construct(
        Cache $cache,
        ApplicationPath $appPath,
        TranslatorInterface $translator,
        LocaleInterface $locale,
        Vendor $vendors,
        XML $xml,
        ModuleAwareRepositoryInterface $systemModuleRepository
    ) {
        $this->cache = $cache;
        $this->appPath = $appPath;
        $this->translator = $translator;
        $this->vendors = $vendors;
        $this->xml = $xml;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return 'infos_' . $this->locale->getLocale();
    }

    /**
     * @return array
     */
    public function getModulesInfoCache()
    {
        if ($this->cache->contains($this->getCacheKey()) === false) {
            $this->saveModulesInfoCache();
        }

        return $this->cache->fetch($this->getCacheKey());
    }

    /**
     * Saves the modules info cache
     */
    public function saveModulesInfoCache()
    {
        $infos = [];

        // 1. fetch all core modules
        // 2. Fetch all 3rd party modules
        // 3. Fetch all local module customizations
        foreach ($this->vendors->getVendors() as $vendor) {
            $infos += $this->fetchVendorModules($vendor);
        }

        $this->cache->save($this->getCacheKey(), $infos);
    }

    /**
     * @param string $vendor
     *
     * @return array
     */
    protected function fetchVendorModules($vendor)
    {
        $infos = [];

        $modules = Filesystem::scandir($this->appPath->getModulesDir() . $vendor . '/');

        if (!empty($modules)) {
            foreach ($modules as $module) {
                $moduleInfo = $this->fetchModuleInfo($module);

                if (!empty($moduleInfo)) {
                    $infos[strtolower($module)] = $moduleInfo;
                }
            }
        }

        return $infos;
    }

    /**
     * @param string $moduleDirectory
     *
     * @return array
     */
    protected function fetchModuleInfo($moduleDirectory)
    {
        $vendors = array_reverse($this->vendors->getVendors()); // Reverse the order of the array -> search module customizations first, then 3rd party modules, then core modules
        foreach ($vendors as $vendor) {
            $moduleXml = $this->appPath->getModulesDir() . $vendor . '/' . $moduleDirectory . '/Resources/config/module.xml';
            $moduleComposerJson = $this->appPath->getModulesDir() . $vendor . '/' . $moduleDirectory . '/composer.json';

            if (is_file($moduleXml) && is_file($moduleComposerJson)) {
                $moduleName = strtolower($moduleDirectory);
                $moduleInfoDb = $this->systemModuleRepository->getInfoByModuleName($moduleName);

                $composer = (new JsonFile($moduleComposerJson))->read();

                return [
                    'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                    'dir' => $moduleDirectory,
                    'installed' => (!empty($moduleInfoDb)),
                    'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1),
                    'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                    'description' => $this->getModuleDescription($composer),
                    'author' => $this->getAuthor($composer),
                    'version' => $this->getModuleVersion($composer),
                    'name' => $this->getModuleName($moduleName),
                    'protected' => $composer['extra']['protected'] ?? false,
                    'installable' => $composer['extra']['installable'] ?? true,
                    'dependencies' => $this->getModuleDependencies($moduleXml),
                ];
            }
        }

        return [];
    }

    /**
     * @param array $composer
     * @return string
     */
    private function getModuleDescription(array $composer): string
    {
        return $composer['description'];
    }

    /**
     * Returns the author of an ACP3 module
     *
     * @param array $composer
     * @return array
     */
    private function getAuthor(array $composer): array
    {
        $authors = [];
        if (isset($composer['authors'])) {
            foreach ($composer['authors'] as $author) {
                $authors[] = $author['name'];
            }
        }

        return $authors;
    }

    /**
     * Returns the version of an ACP3 module
     * @param array $composer
     * @return string
     */
    private function getModuleVersion(array $composer): string
    {
        return $composer['version'] ?? 'N/A';
    }

    /**
     * @param string $moduleName
     * @return string
     */
    private function getModuleName(string $moduleName)
    {
        return $this->translator->t($moduleName, $moduleName);
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
