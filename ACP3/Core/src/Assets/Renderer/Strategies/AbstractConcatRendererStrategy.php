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
use Psr\Cache\CacheItemPoolInterface;

abstract class AbstractConcatRendererStrategy implements RendererStrategyInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly UserModelInterface $userModel,
        protected readonly Assets $assets,
        protected readonly Assets\Libraries $libraries,
        private readonly ApplicationPath $appPath,
        protected readonly CacheItemPoolInterface $coreCachePool,
        protected readonly Modules $modules,
        protected readonly FileResolver $fileResolver,
        private readonly ThemePathInterface $themePath)
    {
    }

    abstract protected function getAssetGroup(): string;

    abstract protected function getFileExtension(): string;

    /**
     * @return string[]
     */
    abstract protected function processLibraries(): array;

    /**
     * This method returns the currently enabled asset libraries as a comma-separated string.
     * It must only contain the library names which are eligible according to the asset group.
     */
    abstract protected function getEnabledLibrariesAsString(): string;

    protected function buildCacheId(): string
    {
        return 'assets_' . $this->generateFilenameHash();
    }

    /**
     * The generated filename hash needs to take the area and the authentication status of the current user into account,
     * as the filenames of the enabled can differ because of these settings.
     */
    private function generateFilenameHash(): string
    {
        return md5(implode(
            '_',
            [
                $this->request->getArea()->value,
                $this->userModel->isAuthenticated(),
                $this->themePath->getCurrentTheme(),
                $this->getEnabledLibrariesAsString(),
                $this->getAssetGroup(),
            ]
        ));
    }

    public function getURI(): ?string
    {
        // We have to initialize the theme here,
        // i.e., enabling the required libraries of the theme + adding theme-specific stylesheets.
        // It has to be called before the "generateFilenameHash" method, otherwise we would get incorrect results!
        $this->assets->initializeTheme();

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
            $this->processLibraries(),
            static fn ($var) => !empty($var)
        );

        if (\count($files) === 0) {
            $webRootPath = null;
        } else {
            $this->saveMinifiedAsset($files, $this->appPath->getUploadsDir() . $path);

            $webRootPath = $this->appPath->getWebRoot() . 'uploads/' . $path;
        }

        $cacheItem->set($webRootPath);
        $this->coreCachePool->saveDeferred($cacheItem);

        return $webRootPath;
    }

    /**
     * @param string[] $files
     */
    private function saveMinifiedAsset(array $files, string $path): void
    {
        $content = [];
        foreach ($files as $file) {
            $content[] = file_get_contents($file) . "\n";
        }

        if (\count($content) === 0) {
            return;
        }

        $this->createAssetsDirectory();

        // Write the contents of the file to the uploads folder
        file_put_contents($path, $this->compress(implode("\n", $content)), LOCK_EX);
    }

    abstract protected function compress(string $assetContent): string;

    private function buildAssetPath(string $filenameHash, int $lastGenerated): string
    {
        return 'assets/' . $filenameHash . '-' . $lastGenerated . '.' . $this->getFileExtension();
    }

    private function createAssetsDirectory(): void
    {
        $concurrentDirectory = $this->appPath->getUploadsDir() . 'assets';
        if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory, 0755) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
