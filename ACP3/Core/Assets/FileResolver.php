<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;
use ACP3\Core\Assets\FileResolver\FileCheckerStrategyInterface;
use ACP3\Core\Component\ComponentRegistry;

class FileResolver
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Assets\Cache
     */
    private $resourcesCache;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;
    /**
     * @var \ACP3\Core\Assets\FileResolver\FileCheckerStrategyInterface[]
     */
    private $strategies = [];

    /**
     * @var array
     */
    private $cachedPaths;
    /**
     * @var bool
     */
    private $newAssetPathsAdded = false;
    /**
     * @var string
     */
    private $designAssetsPath;
    /**
     * @var string
     */
    private $currentTheme;

    public function __construct(
        Core\Assets\Cache $resourcesCache,
        Core\Environment\ApplicationPath $appPath,
        Core\Environment\ThemePathInterface $theme
    ) {
        $this->resourcesCache = $resourcesCache;
        $this->appPath = $appPath;
        $this->cachedPaths = $resourcesCache->getCache();
        $this->theme = $theme;

        $this->addStrategy(new Core\Assets\FileResolver\MinifiedAwareFileCheckerStrategy());
        $this->addStrategy(new Core\Assets\FileResolver\StraightFileCheckerStrategy());
    }

    /**
     * Write newly added assets paths into the cache.
     */
    public function __destruct()
    {
        if ($this->newAssetPathsAdded === true) {
            $this->resourcesCache->saveCache($this->cachedPaths);
        }
    }

    public function addStrategy(FileCheckerStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param string $templatePath
     *
     * @return string
     */
    public function resolveTemplatePath(string $templatePath): string
    {
        // A path without any slash was given -> has to be a layout file of the current design
        if (\strpos($templatePath, '/') === false) {
            return $this->getStaticAssetPath('', '', $templatePath);
        }

        // Split the template path in its components
        $fragments = \explode('/', \ucfirst($templatePath));

        if (isset($fragments[2])) {
            $fragments[1] = \ucfirst($fragments[1]);
        }

        $moduleName = $fragments[0];
        $templatePath = \implode('/', \array_slice($fragments, 1));

        return $this->getStaticAssetPath($moduleName, 'View', $templatePath);
    }

    /**
     * @param string $moduleName
     * @param string $resourceDirectory
     * @param string $file
     *
     * @return string
     */
    public function getStaticAssetPath(
        string $moduleName,
        string $resourceDirectory = '',
        string $file = ''
    ): string {
        if (!empty($resourceDirectory) && !\preg_match('=/$=', $resourceDirectory)) {
            $resourceDirectory .= '/';
        }

        $systemAssetPath = $moduleName . '-' . $resourceDirectory . '-' . $file;
        if (!isset($this->cachedPaths[$systemAssetPath])) {
            $this->cachedPaths[$systemAssetPath] = $this->resolveAssetPath($moduleName, $resourceDirectory, $file);

            $this->newAssetPathsAdded = true;
        }

        return $this->cachedPaths[$systemAssetPath] ?: '';
    }

    /**
     * @param string $moduleName
     * @param string $resourceDirectory
     * @param string $file
     *
     * @return string|null
     */
    private function resolveAssetPath(string $moduleName, string $resourceDirectory, string $file): ?string
    {
        if ($this->designAssetsPath === null) {
            $this->resetTheme();
        }

        $assetPath = $this->findAssetInInheritedThemes(
            \ucfirst($moduleName),
            !empty($resourceDirectory) ? '/' . $resourceDirectory : '',
            $file
        );

        return $assetPath ?: $this->findAssetInModules(
            $moduleName,
            '/Resources/' . $resourceDirectory,
            $file
        );
    }

    /**
     * @param string $moduleName
     * @param string $resourceDirectory
     * @param string $file
     *
     * @return string|null
     */
    private function findAssetInInheritedThemes(string $moduleName, string $resourceDirectory, string $file): ?string
    {
        $designAssetPath = $this->designAssetsPath . $moduleName . $resourceDirectory . $file;

        if (null !== ($resourcePath = $this->findAssetInStrategies($designAssetPath))) {
            return $resourcePath;
        }

        $parentThemes = $this->theme->getThemeDependencies($this->currentTheme);
        $parentTheme = \next($parentThemes);

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
        $this->designAssetsPath = $this->appPath->getDesignRootPathInternal() . $themeName . '/';
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
        } catch (Core\Component\Exception\ComponentNotFoundException $e) {
            // Intentionally omitted
        }

        return null;
    }
}
