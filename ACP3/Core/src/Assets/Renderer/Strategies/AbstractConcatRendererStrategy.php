<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Psr\Cache\CacheItemPoolInterface;

abstract class AbstractConcatRendererStrategy implements RendererStrategyInterface
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var CacheItemPoolInterface
     */
    protected $coreCachePool;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    protected $fileResolver;
    /**
     * @var \ACP3\Core\Assets\Libraries
     */
    protected $libraries;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var SettingsInterface
     */
    private $config;

    public function __construct(
        Assets $assets,
        Assets\Libraries $libraries,
        ApplicationPath $appPath,
        CacheItemPoolInterface $coreCachePool,
        SettingsInterface $config,
        Modules $modules,
        FileResolver $fileResolver
    ) {
        $this->assets = $assets;
        $this->appPath = $appPath;
        $this->coreCachePool = $coreCachePool;
        $this->config = $config;
        $this->modules = $modules;
        $this->fileResolver = $fileResolver;
        $this->libraries = $libraries;
    }

    abstract protected function getAssetGroup(): string;

    abstract protected function getFileExtension(): string;

    abstract protected function processLibraries(): array;

    /**
     * This methods returns the currently enabled asset libraries as a comma-separated string.
     * It must only contain the libraries names which are eligible according to the asset group.
     */
    abstract protected function getEnabledLibrariesAsString(): string;

    protected function buildCacheId(): string
    {
        return 'assets_' . $this->generateFilenameHash();
    }

    private function generateFilenameHash(): string
    {
        $filename = $this->config->getSettings(Schema::MODULE_NAME)['design'];
        $filename .= '_' . $this->getEnabledLibrariesAsString();
        $filename .= '_' . $this->getAssetGroup();

        return md5($filename);
    }

    public function getURI(): string
    {
        // We have to initialize the theme here,
        // i.e. enabling the required libraries of the theme + adding theme specific stylesheets and javascript files.
        // It has to be called before the "generateFilenameHash" method, otherwise we would get incorrect results!
        $this->assets->initializeTheme();

        $filenameHash = $this->generateFilenameHash();
        $cacheId = 'assets-last-generated-' . $filenameHash;

        $cacheItem = $this->coreCachePool->getItem($cacheId);

        if (!$cacheItem->isHit() || false === ($lastGenerated = $cacheItem->get())) {
            $lastGenerated = time(); // Assets are not cached -> set the current time as the new timestamp
        }

        $path = $this->buildAssetPath($filenameHash, $lastGenerated);

        // If the requested minified StyleSheet and/or the JavaScript file doesn't exist, generate it
        if (is_file($this->appPath->getUploadsDir() . $path) === false) {
            // Get the enabled libraries and filter out empty entries
            $files = array_filter(
                $this->processLibraries(),
                static function ($var) {
                    return !empty($var);
                }
            );

            $this->saveMinifiedAsset($files, $this->appPath->getUploadsDir() . $path);

            // Save the time of the generation of the requested file
            $cacheItem->set($lastGenerated);
            $this->coreCachePool->saveDeferred($cacheItem);
        }

        return $this->appPath->getWebRoot() . 'uploads/' . $path;
    }

    private function saveMinifiedAsset(array $files, string $path): void
    {
        $content = [];
        foreach ($files as $file) {
            $content[] = file_get_contents($file) . "\n";
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
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
