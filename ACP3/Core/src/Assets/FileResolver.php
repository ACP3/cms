<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\FileResolver\FileCheckerStrategyInterface;
use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\Exception\ComponentNotFoundException;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;

class FileResolver
{
    /**
     * @var FileCheckerStrategyInterface[]
     */
    private array $strategies = [];

    /**
     * @var string[]
     */
    private ?array $cachedPaths = null;

    private ?string $designAssetsPath = null;

    private ?string $currentTheme = null;

    public function __construct(
        private readonly ApplicationPath $appPath,
        private readonly ThemePathInterface $theme,
    ) {
    }

    public function addStrategy(FileCheckerStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    public function resolveTemplatePath(string $templatePath): string
    {
        if (!str_contains($templatePath, '/')) {
            throw new \InvalidArgumentException(\sprintf('The provided template path "%s" is missing the module name!', $templatePath));
        }

        // Split the template path in its components
        $fragments = explode('/', ucfirst($templatePath));

        if (isset($fragments[2])) {
            $fragments[1] = ucfirst($fragments[1]);
        }

        $moduleName = $fragments[0];
        $templatePath = implode('/', \array_slice($fragments, 1));

        return $this->getStaticAssetPath($moduleName, 'View', $templatePath)[0] ?? '';
    }

    /**
     * @return string[]
     */
    public function getWebStaticAssetPath(
        string $moduleName,
        string $resourceDirectory = '',
        string $file = '',
    ): array {
        $paths = $this->getStaticAssetPath($moduleName, $resourceDirectory, $file);

        if (empty($paths)) {
            return [];
        }

        return array_map(
            function ($path) {
                return $this->appPath->getWebRoot() . str_replace(DIRECTORY_SEPARATOR, '/', substr($path, \strlen(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR)));
            },
            $paths
        );
    }

    /**
     * @return string[]
     */
    public function getStaticAssetPath(
        string $moduleName,
        string $resourceDirectory = '',
        string $file = '',
        bool $addCacheBuster = true,
    ): array {
        if (!empty($resourceDirectory) && !str_ends_with($resourceDirectory, '/')) {
            $resourceDirectory .= '/';
        }
        if (!str_starts_with($resourceDirectory, '/Resources/')) {
            $resourceDirectory = '/Resources/' . $resourceDirectory;
        }

        $cacheKey = $moduleName . '-' . $resourceDirectory . '-' . $file;
        if (!isset($this->cachedPaths[$cacheKey])) {
            $this->cachedPaths[$cacheKey] = $this->resolveAssetPath($moduleName, $resourceDirectory, $file);
        }

        $resolvedPaths = $this->cachedPaths[$cacheKey] ?: [];

        if ($addCacheBuster === true) {
            return $resolvedPaths;
        }

        return array_map(static fn ($path) => substr($path, 0, strrpos($path, '?')), $resolvedPaths);
    }

    /**
     * @return string[]|null
     */
    private function resolveAssetPath(string $moduleName, string $resourceDirectory, string $file): ?array
    {
        if ($this->designAssetsPath === null) {
            $this->resetTheme();
        }

        $assetPaths = $this->findAssetInInheritedThemes(
            ucfirst($moduleName),
            $resourceDirectory,
            $file
        );

        $finalPaths = $assetPaths ?: $this->findAssetInModules($moduleName, $resourceDirectory, $file);

        return $finalPaths !== null ? array_map(static fn ($finalPath) => (string) $finalPath, $finalPaths) : null;
    }

    /**
     * @return string[]|null
     */
    private function findAssetInInheritedThemes(string $moduleName, string $resourceDirectory, string $file): ?array
    {
        $designAssetPath = $this->designAssetsPath . $moduleName . $resourceDirectory . $file;

        if (null !== ($resourcePaths = $this->findAssetInStrategies($designAssetPath))) {
            return $resourcePaths;
        }

        $parentThemes = $this->theme->getThemeDependencies($this->currentTheme);
        $parentTheme = next($parentThemes);

        // Recursively iterate over the nested themes
        if ($parentTheme !== false) {
            $this->changeTheme($parentTheme);
            $assetPaths = $this->getStaticAssetPath($moduleName, $resourceDirectory, $file);
            $this->resetTheme();

            return $assetPaths;
        }

        return null;
    }

    /**
     * @return string[]|null
     */
    private function findAssetInStrategies(string $resourcePath): ?array
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->isAllowed($resourcePath) && (null !== ($resources = $strategy->findResource($resourcePath)))) {
                return $resources;
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

    /**
     * @return string[]|null
     */
    private function findAssetInModules(string $moduleName, string $resourceDirectory, string $file): ?array
    {
        try {
            $moduleAssetPath = ComponentRegistry::getPathByName($moduleName) . $resourceDirectory . $file;

            if (null !== ($resourcePaths = $this->findAssetInStrategies($moduleAssetPath))) {
                return $resourcePaths;
            }
        } catch (ComponentNotFoundException) {
            // Intentionally omitted
        }

        return null;
    }
}
