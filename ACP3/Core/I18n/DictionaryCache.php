<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\Theme;
use ACP3\Core\Modules;
use ACP3\Core\Modules\Vendor;
use Fisharebest\Localization\Locale;

class DictionaryCache implements DictionaryCacheInterface
{
    use ExtractFromPathTrait;

    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var \ACP3\Core\Environment\Theme
     */
    private $theme;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    /**
     * DictionaryCache constructor.
     *
     * @param \ACP3\Core\Cache                       $cache
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules                     $modules
     * @param \ACP3\Core\Modules\Vendor              $vendors
     * @param \ACP3\Core\Environment\Theme           $theme
     */
    public function __construct(
        Cache $cache,
        ApplicationPath $appPath,
        Modules $modules,
        Vendor $vendors,
        Theme $theme
    ) {
        $this->cache = $cache;
        $this->appPath = $appPath;
        $this->vendors = $vendors;
        $this->theme = $theme;
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getLanguageCache(string $language): array
    {
        if ($this->cache->contains($language) === false) {
            $this->saveLanguageCache($language);
        }

        return $this->cache->fetch($language);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function saveLanguageCache(string $language): bool
    {
        $locale = Locale::create($language);
        $data = [
            'info' => [
                'direction' => $locale->script()->direction(),
            ],
            'keys' => [],
        ];

        foreach ($this->modules->getAllModulesTopSorted() as $module) {
            $i18nFile = "{$this->appPath->getModulesDir()}{$module['vendor']}/{$module['dir']}/Resources/i18n/{$language}.xml";

            if (\is_file($i18nFile) === false) {
                continue;
            }

            $data['keys'] += $this->parseI18nFile($i18nFile, $module['dir']);
        }

        $themeDependenciesReversed = \array_reverse($this->theme->getCurrentThemeDependencies());
        foreach ($themeDependenciesReversed as $theme) {
            $i18nFiles = \glob(ACP3_ROOT_DIR . "designs/{$theme}/*/i18n/{$language}.xml");

            if (\count($i18nFiles) === 0) {
                continue;
            }

            foreach ($i18nFiles as $i18nFile) {
                $data['keys'] = \array_merge(
                    $data['keys'],
                    $this->parseI18nFile($i18nFile, $this->getModuleNameFromThemePath($i18nFile))
                );
            }
        }

        return $this->cache->save($language, $data);
    }

    /**
     * @param string $i18nFile
     * @param string $moduleName
     *
     * @return array
     */
    private function parseI18nFile(string $i18nFile, string $moduleName): array
    {
        $data = [];
        $xml = \simplexml_load_file($i18nFile);
        foreach ($xml->keys->item as $item) {
            $data[\strtolower($moduleName . (string) $item['key'])] = \trim((string) $item);
        }

        return $data;
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    private function getModuleNameFromThemePath(string $filePath): string
    {
        $pathArray = \explode('/', $filePath);

        return $pathArray[\count($pathArray) - 3];
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguagePacksCache(): array
    {
        if ($this->cache->contains('language_packs') === false) {
            $this->saveLanguagePacksCache();
        }

        return $this->cache->fetch('language_packs');
    }

    /**
     * Sets the cache for all registered languages.
     *
     * @return bool
     */
    protected function saveLanguagePacksCache(): bool
    {
        $languagePacks = [];
        $languageFiles = \glob($this->appPath->getModulesDir() . '*/*/Resources/i18n/*.xml');

        if ($languageFiles !== false) {
            foreach ($languageFiles as $file) {
                $languagePack = $this->registerLanguagePack($file);

                if (empty($languagePack)) {
                    continue;
                }

                $languagePacks += $languagePack;
            }
        }

        return $this->cache->save('language_packs', $languagePacks);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    protected function registerLanguagePack(string $file): array
    {
        $languageIso = $this->getLanguagePackIsoCode($file);

        try {
            $locale = Locale::create($languageIso);

            return [
                $languageIso => [
                    'iso' => $languageIso,
                    'name' => $locale->endonym(),
                ],
            ];
        } catch (\DomainException $e) {
            return [];
        }
    }
}
