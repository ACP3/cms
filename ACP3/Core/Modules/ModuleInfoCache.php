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
                $moduleInfo = $this->xml->parseXmlFile($moduleXml, 'info');

                if (!empty($moduleInfo)) {
                    $moduleName = strtolower($moduleDirectory);
                    $moduleInfoDb = $this->systemModuleRepository->getInfoByModuleName($moduleName);

                    $composer = (new JsonFile($moduleComposerJson))->read();

                    return [
                        'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                        'dir' => $moduleDirectory,
                        'installed' => (!empty($moduleInfoDb)),
                        'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1),
                        'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                        'description' => $composer['description'] ?? $this->getModuleDescription($moduleInfo, $moduleName),
                        'author' => $this->getAuthor($composer, $moduleInfo),
                        'version' => $this->getModuleVersion($composer, $moduleInfo),
                        'name' => $this->getModuleName($moduleInfo, $moduleName),
                        'categories' => isset($moduleInfo['categories']),
                        'protected' => isset($moduleInfo['protected']),
                        'installable' => !isset($moduleInfo['no_install']),
                        'dependencies' => $this->getModuleDependencies($moduleXml),
                    ];
                }
            }
        }

        return [];
    }

    /**
     * @param array  $moduleInfo
     * @param string $moduleName
     *
     * @return string
     */
    protected function getModuleDescription(array $moduleInfo, $moduleName)
    {
        if (isset($moduleInfo['description']['lang']) && $moduleInfo['description']['lang'] === 'true') {
            return $this->translator->t($moduleName, 'mod_description');
        }

        return $moduleInfo['description'];
    }

    /**
     * Returns the author of an ACP3 module
     *
     * @param array $composerInfo
     * @param array $moduleXmlInfo
     * @return array
     */
    private function getAuthor(array $composerInfo, array $moduleXmlInfo): array
    {
        $authors = [];
        if (isset($composerInfo['authors'])) {
            foreach ($composerInfo['authors'] as $author) {
                $authors[] = $author['name'];
            }
        } else {
            $authors[] = $moduleXmlInfo['author'];
        }

        return $authors;
    }

    /**
     * Returns the version of an ACP3 module
     * @param array $composerInfo
     * @param array $moduleXmlInfo
     * @return string
     */
    private function getModuleVersion(array $composerInfo, array $moduleXmlInfo): string
    {
        return $composerInfo['version'] ?? $moduleXmlInfo['version'];
    }

    /**
     * @param array  $moduleInfo
     * @param string $moduleName
     *
     * @return string
     */
    protected function getModuleName(array $moduleInfo, $moduleName)
    {
        if (isset($moduleInfo['name']['lang']) && $moduleInfo['name']['lang'] === 'true') {
            return $this->translator->t($moduleName, $moduleName);
        }

        return $moduleInfo['name'];
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
