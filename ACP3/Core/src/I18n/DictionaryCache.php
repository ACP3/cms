<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Cache;
use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Environment\ThemePathInterface;
use DomainException;
use Fisharebest\Localization\Locale;

class DictionaryCache implements DictionaryCacheInterface
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;

    public function __construct(
        Cache $cache,
        ThemePathInterface $theme
    ) {
        $this->cache = $cache;
        $this->theme = $theme;
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

        $components = ComponentRegistry::filterByType(
            ComponentRegistry::allTopSorted(),
            [
                ComponentTypeEnum::CORE,
                ComponentTypeEnum::MODULE,
                ComponentTypeEnum::INSTALLER,
            ]
        );

        foreach ($components as $component) {
            $i18nFile = "{$component->getPath()}/Resources/i18n/{$language}.xml";

            if (\is_file($i18nFile) === false) {
                continue;
            }

            $data['keys'] += $this->parseI18nFile($i18nFile, $component->getName());
        }

        $themeDependenciesReversed = \array_reverse($this->theme->getCurrentThemeDependencies());
        foreach ($themeDependenciesReversed as $theme) {
            $i18nFiles = \glob($this->theme->getDesignPathInternal($theme) . "/*/i18n/{$language}.xml");

            if ($i18nFiles === false) {
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

    private function parseI18nFile(string $i18nFile, string $moduleName): array
    {
        $data = [];
        $xml = \simplexml_load_string(\file_get_contents($i18nFile));
        foreach ($xml->keys->item as $item) {
            $data[\strtolower($moduleName . $item['key'])] = \trim((string) $item);
        }

        return $data;
    }

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
     */
    protected function saveLanguagePacksCache(): bool
    {
        $languagePacks = [];

        foreach (ComponentRegistry::all() as $component) {
            $languageFiles = \glob($component->getPath() . '/Resources/i18n/*.xml');

            if ($languageFiles === false) {
                continue;
            }

            foreach ($languageFiles as $file) {
                $isoCode = $this->getLanguagePackIsoCode($file);

                if (isset($languagePacks[$isoCode])) {
                    continue;
                }

                try {
                    $languagePacks[$isoCode] = $this->getLanguagePack($isoCode);
                } catch (DomainException $e) {
                    // Intentionally omitted
                }
            }
        }

        return $this->cache->save('language_packs', $languagePacks);
    }

    /**
     * @throws DomainException
     */
    private function getLanguagePack(string $languageIsoCode): array
    {
        return [
            'iso' => $languageIsoCode,
            'name' => Locale::create($languageIsoCode)->endonym(),
        ];
    }

    private function getLanguagePackIsoCode(string $filePath): string
    {
        return \pathinfo($filePath)['filename'];
    }
}
