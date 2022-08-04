<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;
use ACP3\Core\Assets\FileResolver\FileCheckerStrategyInterface;
use ACP3\Core\Component\ComponentRegistry;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class FileResolver
{
    private const CACHE_KEY = 'resources';

    /**
     * @var FileCheckerStrategyInterface[]
     */
    private array $strategies = [];

    /**
     * @var string[]
     */
    private ?array $cachedPaths = null;

    private ?CacheItemInterface $cacheItem = null;

    private ?string $designAssetsPath = null;

    private ?string $currentTheme = null;

    public function __construct(
        private readonly CacheItemPoolInterface $coreCachePool,
        private readonly Core\Environment\ApplicationPath $appPath,
        private readonly Core\Environment\ThemePathInterface $theme
    ) {
        $this->addStrategy(new Core\Assets\FileResolver\MinifiedAwareFileCheckerStrategy());
        $this->addStrategy(new Core\Assets\FileResolver\StraightFileCheckerStrategy());
    }

    public function addStrategy(FileCheckerStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    public function resolveTemplatePath(string $templatePath): string
    {
        if (!str_contains($templatePath, '/')) {
            throw new \InvalidArgumentException(sprintf('The provided template path "%s" is missing the module name!', $templatePath));
        }

        // Split the template path in its components
        $fragments = explode('/', ucfirst($templatePath));

        if (isset($fragments[2])) {
            $fragments[1] = ucfirst($fragments[1]);
        }

        $moduleName = $fragments[0];
        $templatePath = implode('/', \array_slice($fragments, 1));

        return $this->getStaticAssetPath($moduleName, 'View', $templatePath);
    }

    public function getWebStaticAssetPath(
        string $moduleName,
        string $resourceDirectory = '',
        string $file = ''
    ): string {
        $path = $this->getStaticAssetPath($moduleName, $resourceDirectory, $file);

        if ($path === '') {
            return '';
        }

        return $this->appPath->getWebRoot() . str_replace(DIRECTORY_SEPARATOR, '/', substr($path, \strlen(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR)));
    }

    public function getStaticAssetPath(
        string $moduleName,
        string $resourceDirectory = '',
        string $file = ''
    ): string {
        if (!empty($resourceDirectory) && !preg_match('=/$=', $resourceDirectory)) {
            $resourceDirectory .= '/';
        }

        if ($this->cachedPaths === null) {
            $cacheItem = $this->coreCachePool->getItem(self::CACHE_KEY);

            if (!$cacheItem->isHit()) {
                $cacheItem->set([]);
            }

            $this->cachedPaths = $cacheItem->get();
            $this->cacheItem = $cacheItem;
        }

        $cacheKey = $moduleName . '-' . $resourceDirectory . '-' . $file;
        if (!isset($this->cachedPaths[$cacheKey])) {
            $this->cachedPaths[$cacheKey] = $this->resolveAssetPath($moduleName, $resourceDirectory, $file);

            $this->cacheItem->set($this->cachedPaths);
            $this->coreCachePool->saveDeferred($this->cacheItem);
        }

        return $this->cachedPaths[$cacheKey] ?: '';
    }

    private function resolveAssetPath(string $moduleName, string $resourceDirectory, string $file): ?string
    {
        if ($this->designAssetsPath === null) {
            $this->resetTheme();
        }

        $assetPath = $this->findAssetInInheritedThemes(
            ucfirst($moduleName),
            !empty($resourceDirectory) ? '/' . $resourceDirectory : '',
            $file
        );

        $finalPath = $assetPath ?: $this->findAssetInModules($moduleName, '/Resources/' . $resourceDirectory, $file);

        return $finalPath !== null ? realpath($finalPath) : null;
    }

    private function findAssetInInheritedThemes(string $moduleName, string $resourceDirectory, string $file): ?string
    {
        $designAssetPath = $this->designAssetsPath . $moduleName . $resourceDirectory . $file;

        if (null !== ($resourcePath = $this->findAssetInStrategies($designAssetPath))) {
            return $resourcePath;
        }

        $parentThemes = $this->theme->getThemeDependencies($this->currentTheme);
        $parentTheme = next($parentThemes);

        // Recursively iterate over the nested themes
        if ($parentTheme !== false) {
            $this->changeTheme($parentTheme);
            $assetPath = $this->getStaticAssetPath($moduleName, $resourceDirectory, $file);
            $this->resetTheme();

            return $assetPath;
        }

        return null;
    }

    private function findAssetInStrategies(string $resourcePath): ?string
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->isAllowed($resourcePath) && (null !== ($resource = $strategy->findResource($resourcePath)))) {
                return $resource;
            }
        }

        return null;
    }

    private function changeTheme(string $themeName): void
    {
        $this->currentTheme = $themeName;
        $this->designAssetsPath = $this->theme->getDesignPathInternal($themeName) . DIRECTORY_SEPARATOR;
    }

    private function resetTheme(): void
    {
        $this->changeTheme($this->theme->getCurrentTheme());
    }

    private function findAssetInModules(string $moduleName, string $resourceDirectory, string $file): ?string
    {
        try {
            $moduleAssetPath = ComponentRegistry::getPathByName($moduleName) . $resourceDirectory . $file;

            if (null !== ($resourcePath = $this->findAssetInStrategies($moduleAssetPath))) {
                return $resourcePath;
            }
        } catch (Core\Component\Exception\ComponentNotFoundException) {
            // Intentionally omitted
        }

        return null;
    }
}
