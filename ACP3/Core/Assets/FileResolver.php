<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;
use ACP3\Core\Assets\FileResolver\FileCheckerStrategyInterface;

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
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\Environment\Theme
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

    /**
     * @param \ACP3\Core\Assets\Cache                $resourcesCache
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Environment\Theme           $theme
     * @param \ACP3\Core\Modules                     $modules
     */
    public function __construct(
        Core\Assets\Cache $resourcesCache,
        Core\Environment\ApplicationPath $appPath,
        Core\Environment\Theme $theme,
        Core\Modules $modules
    ) {
        $this->resourcesCache = $resourcesCache;
        $this->appPath = $appPath;
        $this->cachedPaths = $resourcesCache->getCache();
        $this->modules = $modules;
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
            return $this->getStaticAssetPath('', '', '', $templatePath);
        }
        // Split the template path in its components
        $fragments = \explode('/', \ucfirst($templatePath));

        if (isset($fragments[2])) {
            $fragments[1] = \ucfirst($fragments[1]);
        }
        $modulesPath = $fragments[0] . '/Resources/';
        $designPath = $fragments[0];
        $templatePath = \implode('/', \array_slice($fragments, 1));

        return $this->getStaticAssetPath($modulesPath, $designPath, 'View', $templatePath);
    }

    /**
     * @param string $modulePath
     * @param string $designPath
     * @param string $dir
     * @param string $file
     *
     * @return string
     */
    public function getStaticAssetPath(
        string $modulePath,
        string $designPath,
        string $dir = '',
        string $file = ''
    ): string {
        if ($this->needsTrailingSlash($modulePath)) {
            $modulePath .= '/';
        }
        if ($this->needsTrailingSlash($designPath)) {
            $designPath .= '/';
        }
        if (!empty($dir) && !\preg_match('=/$=', $dir)) {
            $dir .= '/';
        }

        $systemAssetPath = $this->appPath->getModulesDir() . $modulePath . $dir . $file;
        if (!isset($this->cachedPaths[$systemAssetPath])) {
            $this->cachedPaths[$systemAssetPath] = $this->resolveAssetPath($modulePath, $designPath, $dir, $file);

            $this->newAssetPathsAdded = true;
        }

        return $this->cachedPaths[$systemAssetPath] ?: '';
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function needsTrailingSlash(string $path): bool
    {
        return $path !== '' && \strpos($path, '.') === false && !\preg_match('=/$=', $path);
    }

    /**
     * @param string $modulePath
     * @param string $designPath
     * @param string $dir
     * @param string $file
     *
     * @return string|null
     */
    private function resolveAssetPath(string $modulePath, string $designPath, string $dir, string $file): ?string
    {
        if ($this->designAssetsPath === null) {
            $this->resetTheme();
        }

        $assetPath = $this->findAssetInInheritedThemes($modulePath, $designPath, $dir, $file);

        return $assetPath ?: $this->findAssetInModules($modulePath, $dir, $file);
    }

    /**
     * @param string $modulePath
     * @param string $designPath
     * @param string $dir
     * @param string $file
     *
     * @return string|null
     */
    private function findAssetInInheritedThemes(string $modulePath, string $designPath, string $dir, string $file): ?string
    {
        $designAssetPath = $this->designAssetsPath . $designPath . $dir . $file;

        if (null !== ($resourcePath = $this->findAssetInStrategies($designAssetPath))) {
            return $resourcePath;
        }

        $parentThemes = $this->theme->getThemeDependencies($this->currentTheme);
        $parentTheme = \next($parentThemes);

        // Recursively iterate over the nested themes
        if ($parentTheme !== false) {
            $this->changeTheme($parentTheme);
            $assetPath = $this->getStaticAssetPath($modulePath, $designPath, $dir, $file);
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

    private function findAssetInModules(string $modulePath, string $dir, string $file): ?string
    {
        $moduleName = \substr($modulePath, 0, \strpos($modulePath, '/'));
        $moduleInfo = $this->modules->getModuleInfo($moduleName);

        if (!empty($moduleInfo)) {
            $moduleAssetPath = $this->appPath->getModulesDir() . $moduleInfo['vendor'] . '/' . $modulePath . $dir . $file;

            if (null !== ($resourcePath = $this->findAssetInStrategies($moduleAssetPath))) {
                return $resourcePath;
            }
        }

        return null;
    }
}
