<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class ConcatCSSRendererStrategy extends CSSRendererStrategy
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly UserModelInterface $userModel,
        private readonly Assets $assets,
        Assets\Libraries $libraries,
        private readonly ApplicationPath $appPath,
        private readonly CacheItemPoolInterface $coreCachePool,
        Modules $modules,
        FileResolver $fileResolver,
        private readonly ThemePathInterface $themePath,
    ) {
        parent::__construct($request, $assets, $libraries, $modules, $fileResolver);
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     * @throws InvalidArgumentException
     */
    public function renderHtmlElement(): string
    {
        $cssUri = $this->getURI();

        if ($cssUri === null) {
            return '';
        }

        return '<link rel="stylesheet" type="text/css" href="' . $cssUri . '">' . "\n";
    }

    /**
     * The generated filename hash needs to take the area and the authentication status of the current user into account,
     * as the filenames of the enabled libraries can differ because of these settings.
     */
    private function generateFilenameHash(): string
    {
        return md5(implode(
            '_',
            [
                $this->request->getArea()->value,
                $this->userModel->isAuthenticated(),
                $this->themePath->getCurrentTheme(),
                $this->request->getPathInfo(),
                'css',
            ]
        ));
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     * @throws InvalidArgumentException
     */
    public function getURI(): ?string
    {
        $filenameHash = $this->generateFilenameHash();
        $cacheId = 'assets-last-generated-' . $filenameHash;

        $cacheItem = $this->coreCachePool->getItem($cacheId);
        $cachedAssetPath = $cacheItem->get();

        if ($cacheItem->isHit()) {
            return $cachedAssetPath;
        }

        $lastGenerated = time(); // Assets are not cached -> set the current time as the new timestamp
        $path = $this->buildAssetPath($filenameHash, $lastGenerated);

        // Get the enabled libraries and filter out empty entries
        $files = array_filter(
            $this->stylesheets,
            static fn ($var) => !empty($var)
        );

        if (\count($files) === 0) {
            $webRootPath = null;
        } else {
            $this->saveConcatenatedAsset($files, $this->appPath->getUploadsDir() . $path);

            $webRootPath = $this->appPath->getWebRoot() . 'uploads/' . $path;
        }

        $cacheItem->set($webRootPath);
        $this->coreCachePool->saveDeferred($cacheItem);

        return $webRootPath;
    }

    /**
     * @param string[] $files
     */
    private function saveConcatenatedAsset(array $files, string $path): void
    {
        $content = [];
        foreach ($files as $file) {
            $content[] = file_get_contents(
                str_contains($file, '.css?') ? substr($file, 0, strrpos($file, '?')) : $file
            );
        }

        if (\count($content) === 0) {
            return;
        }

        $this->createAssetsDirectory();

        // Write the contents of the file to the uploads folder
        file_put_contents($path, implode('', $content), LOCK_EX);
    }

    private function buildAssetPath(string $filenameHash, int $lastGenerated): string
    {
        return 'assets/' . $filenameHash . '-' . $lastGenerated . '.css';
    }

    private function createAssetsDirectory(): void
    {
        $concurrentDirectory = $this->appPath->getUploadsDir() . 'assets';
        if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory, 0755) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     * @throws InvalidArgumentException
     */
    public function initialize(): void
    {
        $cacheId = $this->buildCacheId();
        $cacheItem = $this->coreCachePool->getItem($cacheId);

        if (!$cacheItem->isHit()) {
            $backup = $this->stylesheets;
            $this->stylesheets = [];

            // We have to initialize the theme here,
            // i.e., enabling the required libraries of the theme + adding theme-specific stylesheets.
            // It has to be called before the "generateFilenameHash" method, otherwise we would get incorrect results!
            $this->assets->initializeTheme();

            $this->fetchLibraries();
            $this->fetchThemeStylesheets();
            $this->fetchModuleStylesheets();

            $this->addFiles($backup);

            $cacheItem->set($this->stylesheets);
            $this->coreCachePool->saveDeferred($cacheItem);
        }
    }

    private function buildCacheId(): string
    {
        return 'assets_' . $this->generateFilenameHash();
    }
}
