<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Services;

use ACP3\Core\Assets\LibrariesCache;
use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Exception\CacheClearException;
use ACP3\Modules\ACP3\System\Exception\InvalidCacheTypeException;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class CacheClearService
{
    /**
     * @var array
     */
    private $cacheTypes;

    public function __construct(
        ApplicationPath $appPath,
        Psr6Store $httpCacheStore,
        SettingsInterface $settings,
        LibrariesCache $librariesCache
    ) {
        $this->cacheTypes = [
            'general' => [
                'dependency' => 'page',
                'paths' => $appPath->getCacheDir() . 'sql',
            ],
            'minify' => [
                'dependency' => 'page',
                'paths' => $appPath->getUploadsDir() . 'assets',
            ],
            'page' => ['paths' => function () use ($httpCacheStore, $settings, $librariesCache) {
                $httpCacheStore->clear();
                $librariesCache->deleteAll();

                $settings->saveSettings(
                    ['page_cache_is_valid' => true],
                    Schema::MODULE_NAME
                );
            }],
            'templates' => ['paths' => $appPath->getCacheDir() . 'tpl_compiled'],
        ];
    }

    /**
     * @return string[]
     */
    public function getCacheTypeKeys(): array
    {
        return \array_keys($this->cacheTypes);
    }

    public function isSupportedCacheType(string $cacheType): bool
    {
        return \array_key_exists($cacheType, $this->cacheTypes);
    }

    /**
     * @return Array<string, string|string[]>
     */
    public function getCacheTypes(): array
    {
        return $this->cacheTypes;
    }

    /**
     * @throws InvalidCacheTypeException
     * @throws CacheClearException
     */
    public function clearCacheByType(string $cacheType): void
    {
        if (!$this->isSupportedCacheType($cacheType)) {
            throw new InvalidCacheTypeException(\sprintf('The given cache type "%s" is not supported!', $cacheType));
        }

        $cacheTypeData = $this->getCacheTypes()[$cacheType];

        if (\is_callable($cacheTypeData['paths'])) {
            $cacheTypeData['paths']();
        } elseif (!Purge::doPurge($cacheTypeData['paths'])) {
            throw new CacheClearException(\sprintf('An error occurred while clearing the cache for type "%s".', $cacheType));
        }

        if (\array_key_exists('dependency', $cacheTypeData)) {
            $this->clearCacheByType($cacheTypeData['dependency']);
        }
    }
}
