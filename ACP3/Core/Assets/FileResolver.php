<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;

class FileResolver
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Assets\Cache
     */
    protected $resourcesCache;
    /**
     * @var array
     */
    protected $cachedPaths = [];
    /**
     * @var bool
     */
    protected $newAssetPathsAdded = false;
    /**
     * @var string
     */
    protected $designAssetsPath;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\Environment\Theme
     */
    private $theme;
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

        // Return early, if the path has been already cached
        if (isset($this->cachedPaths[$systemAssetPath])) {
            return $this->cachedPaths[$systemAssetPath];
        }

        return $this->resolveAssetPath($modulePath, $designPath, $dir, $file);
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
     * @return string
     */
    private function resolveAssetPath(string $modulePath, string $designPath, string $dir, string $file): string
    {
        if ($this->designAssetsPath === null) {
            $this->resetDesignAssetPath();
        }

        $assetPath = '';
        $designAssetPath = $this->designAssetsPath . $designPath . $dir . $file;

        // A theme has overridden a static asset of a module
        if (\is_file($designAssetPath) === true) {
            $assetPath = $designAssetPath;
        } else {
            $parentThemes = $this->theme->getThemeDependencies($this->currentTheme);
            $parentTheme = \next($parentThemes);

            // Recursively iterate over the nested themes
            if ($parentTheme !== false) {
                $this->modifyDesignAssetPath($parentTheme);
                $assetPath = $this->getStaticAssetPath($modulePath, $designPath, $dir, $file);
                $this->resetDesignAssetPath();

                return $assetPath;
            }

            // No overrides have been found -> iterate over all possible module namespaces
            $moduleName = \substr($modulePath, 0, \strpos($modulePath, '/'));
            $moduleInfo = $this->modules->getModuleInfo($moduleName);

            if (!empty($moduleInfo)) {
                $moduleAssetPath = $this->appPath->getModulesDir() . $moduleInfo['vendor'] . '/' . $modulePath . $dir . $file;
                if (\is_file($moduleAssetPath) === true) {
                    $assetPath = $moduleAssetPath;
                }
            }
        }

        $systemAssetPath = $this->appPath->getModulesDir() . $modulePath . $dir . $file;
        $this->cachedPaths[$systemAssetPath] = $assetPath;
        $this->newAssetPathsAdded = true;

        return $assetPath;
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function resolveTemplatePath(string $template): string
    {
        // A path without any slash was given -> has to be a layout file of the current design
        if (\strpos($template, '/') === false) {
            return $this->getStaticAssetPath('', '', '', $template);
        }
        // Split the template path in its components
        $fragments = \explode('/', \ucfirst($template));

        if (isset($fragments[2])) {
            $fragments[1] = \ucfirst($fragments[1]);
        }
        $modulesPath = $fragments[0] . '/Resources/';
        $designPath = $fragments[0];
        $template = \implode('/', \array_slice($fragments, 1));

        return $this->getStaticAssetPath($modulesPath, $designPath, 'View', $template);
    }

    private function modifyDesignAssetPath(string $themeName): void
    {
        $this->currentTheme = $themeName;
        $this->designAssetsPath = $this->appPath->getDesignRootPathInternal() . $themeName . '/';
    }

    private function resetDesignAssetPath(): void
    {
        $this->modifyDesignAssetPath($this->theme->getCurrentTheme());
    }
}
