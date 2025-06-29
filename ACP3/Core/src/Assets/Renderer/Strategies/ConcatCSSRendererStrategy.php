<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use Psr\Cache\CacheItemPoolInterface;
use tubalmartin\CssMin\Minifier;

class ConcatCSSRendererStrategy implements CSSRendererStrategyInterface
{
    protected const ASSETS_PATH_CSS = 'Assets/css';

    /**
     * @var string[]
     */
    protected array $stylesheets = [];

    public function __construct(
        private readonly RequestInterface $request,
        private readonly Minifier $minifier,
        private readonly UserModelInterface $userModel,
        private readonly Assets $assets,
        private readonly Assets\Libraries $libraries,
        private readonly ApplicationPath $appPath,
        private readonly CacheItemPoolInterface $coreCachePool,
        private readonly Modules $modules,
        private readonly FileResolver $fileResolver,
        private readonly ThemePathInterface $themePath,
    ) {
    }

    protected function getAssetGroup(): string
    {
        return 'css';
    }

    protected function getFileExtension(): string
    {
        return 'css';
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function getEnabledLibrariesAsString(): string
    {
        return implode(',', array_map(static fn (LibraryEntity $library) => $library->getLibraryIdentifier(), $this->getEnabledLibraries()));
    }

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

    /**
     * @return LibraryEntity[]
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function getEnabledLibraries(): array
    {
        return array_filter($this->libraries->getEnabledLibraries(), static fn (LibraryEntity $library) => !empty($library->getCss()));
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function processLibraries(): array
    {
        $cacheId = $this->buildCacheId();
        $cacheItem = $this->coreCachePool->getItem($cacheId);

        if (!$cacheItem->isHit()) {
            $this->fetchLibraries();
            $this->fetchThemeStylesheets();
            $this->fetchModuleStylesheets();

            $cacheItem->set($this->stylesheets);
            $this->coreCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function fetchLibraries(): void
    {
        foreach ($this->getEnabledLibraries() as $library) {
            foreach ($library->getCss() as $stylesheet) {
                $this->addFiles($this->fileResolver->getStaticAssetPath(
                    $library->getModuleName(),
                    static::ASSETS_PATH_CSS,
                    $stylesheet
                ),
                );
            }
        }
    }

    public function addFiles(array $files): void
    {
        foreach ($files as $file) {
            if (!empty($file) && str_ends_with($file, '.css') && !\in_array($file, $this->stylesheets, true)) {
                $this->stylesheets[] = $file;
            }
        }
    }

    /**
     * Fetches the theme stylesheets.
     */
    private function fetchThemeStylesheets(): void
    {
        foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
            $this->addFiles(
                $this->fileResolver->getStaticAssetPath(
                    'System',
                    static::ASSETS_PATH_CSS,
                    trim($file)
                ),
            );
        }

        $this->addFiles(
            $this->fileResolver->getStaticAssetPath(
                'System',
                static::ASSETS_PATH_CSS,
                'layout.css'
            ),
        );
    }

    /**
     * Fetches the stylesheets of all currently enabled modules.
     */
    private function fetchModuleStylesheets(): void
    {
        $area = $this->request->getArea();

        foreach ($this->modules->getInstalledModules() as $module) {
            $this->addFiles(
                $this->fileResolver->getStaticAssetPath(
                    $module['name'],
                    static::ASSETS_PATH_CSS,
                    'style.css'
                ),
            );

            if ($area === AreaEnum::AREA_ADMIN) {
                $this->addFiles(
                    $this->fileResolver->getStaticAssetPath(
                        $module['name'],
                        static::ASSETS_PATH_CSS,
                        'admin.css'
                    ),
                );
            }

            // Append custom styles to the default module styling
            $this->addFiles(
                $this->fileResolver->getStaticAssetPath(
                    $module['name'],
                    static::ASSETS_PATH_CSS,
                    'append.css'
                ),
            );
        }
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        $cssUri = $this->getURI();

        if ($cssUri === null) {
            return '';
        }

        return '<link rel="stylesheet" type="text/css" href="' . $this->getURI() . '">' . "\n";
    }

    protected function compress(string $assetContent): string
    {
        return $this->minifier->run($assetContent);
    }
}
