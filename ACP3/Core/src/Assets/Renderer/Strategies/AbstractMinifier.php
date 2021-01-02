<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use JSMin\JSMin;
use Psr\Log\LoggerInterface;

abstract class AbstractMinifier implements MinifierInterface, RendererStrategyInterface
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $systemCache;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    protected $fileResolver;
    /**
     * @var string
     */
    protected $environment;
    /**
     * @var \ACP3\Core\Assets\Libraries
     */
    protected $libraries;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        Assets $assets,
        Assets\Libraries $libraries,
        ApplicationPath $appPath,
        Cache $systemCache,
        SettingsInterface $config,
        Modules $modules,
        FileResolver $fileResolver,
        string $environment
    ) {
        $this->assets = $assets;
        $this->appPath = $appPath;
        $this->systemCache = $systemCache;
        $this->config = $config;
        $this->modules = $modules;
        $this->fileResolver = $fileResolver;
        $this->environment = $environment;
        $this->logger = $logger;
        $this->libraries = $libraries;
    }

    abstract protected function getAssetGroup(): string;

    abstract protected function getFileExtension(): string;

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function buildCacheId(string $layout): string
    {
        return 'assets_' . $this->generateFilenameHash($layout);
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function generateFilenameHash(string $layout): string
    {
        $filename = $this->config->getSettings(Schema::MODULE_NAME)['design'];
        $filename .= '_' . $layout;
        $filename .= '_' . $this->libraries->getEnabledLibrariesAsString();
        $filename .= '_' . $this->getAssetGroup();

        return \md5($filename);
    }

    abstract protected function processLibraries(string $layout): array;

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getURI(string $layout = 'layout'): string
    {
        // We have to initialize the theme here,
        // i.e. enabling the required libraries of the theme + adding theme specific stylesheets and javascript files.
        // It has to be called before the "generateFilenameHash" method, otherwise would get incorrect results!
        $this->assets->initializeTheme();

        $debug = $this->environment === ApplicationMode::DEVELOPMENT;
        $filenameHash = $this->generateFilenameHash($layout);
        $cacheId = 'assets-last-generated-' . $filenameHash;

        if (false === ($lastGenerated = $this->systemCache->fetch($cacheId))) {
            $lastGenerated = \time(); // Assets are not cached -> set the current time as the new timestamp
        }

        $path = $this->buildAssetPath($debug, $filenameHash, $lastGenerated);

        // If the requested minified StyleSheet and/or the JavaScript file doesn't exist, generate it
        if ($debug === true || \is_file($this->appPath->getUploadsDir() . $path) === false) {
            // Get the enabled libraries and filter out empty entries
            $files = \array_filter(
                $this->processLibraries($layout),
                static function ($var) {
                    return !empty($var);
                }
            );

            $this->saveMinifiedAsset($files, $this->appPath->getUploadsDir() . $path);

            // Save the time of the generation of the requested file
            $this->systemCache->save($cacheId, $lastGenerated);
        }

        return $this->appPath->getWebRoot() . 'uploads/' . $path . ($debug === true ? '?v=' . $lastGenerated : '');
    }

    protected function saveMinifiedAsset(array $files, string $path): void
    {
        $options = [
            'options' => [
                \Minify::TYPE_CSS => [\Minify_CSSmin::class, 'minify'],
                \Minify::TYPE_JS => [JSMin::class, 'minify'],
            ],
        ];

        $minify = new \Minify(new \Minify_Cache_Null(), $this->logger);
        $content = $minify->combine($files, $options);

        $this->createAssetsDirectory();

        // Write the contents of the file to the uploads folder
        \file_put_contents($path, $content, LOCK_EX);
    }

    protected function buildAssetPath(bool $debug, string $filenameHash, int $lastGenerated): string
    {
        if ($debug === true) {
            return 'assets/' . $filenameHash . '.' . $this->getFileExtension();
        }

        return 'assets/' . $filenameHash . '-' . $lastGenerated . '.' . $this->getFileExtension();
    }

    private function createAssetsDirectory(): void
    {
        $concurrentDirectory = $this->appPath->getUploadsDir() . 'assets';
        if (!\is_dir($concurrentDirectory) && !\mkdir($concurrentDirectory, 0755) && !\is_dir($concurrentDirectory)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
