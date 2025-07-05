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
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use tubalmartin\CssMin\Minifier;

class ConcatCSSRendererStrategy implements CSSRendererStrategyInterface
{
    public function __construct(
        private readonly CSSRendererStrategy $cssRendererStrategy,
        private readonly RequestInterface $request,
        private readonly UserModelInterface $userModel,
        private readonly ApplicationPath $appPath,
        private readonly CacheItemPoolInterface $coreCachePool,
        private readonly FileResolver $fileResolver,
        private readonly ThemePathInterface $themePath,
        private readonly Minifier $minifier,
    ) {
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
     * as the filenames of the included assets can differ because of these settings.
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

        $path = $this->buildAssetPath();

        if (\count($this->cssRendererStrategy->getFiles()) === 0) {
            $webRootPath = null;
        } else {
            $this->saveConcatenatedAsset($path);

            $webRootPath = $this->fileResolver->rewriteToWebStaticAssetPath($path);
        }

        $cacheItem->set($webRootPath);
        $this->coreCachePool->saveDeferred($cacheItem);

        return $webRootPath;
    }

    private function saveConcatenatedAsset(string $path): void
    {
        if (is_file($path)) {
            return;
        }

        $content = [];
        foreach ($this->cssRendererStrategy->getFiles() as $file => $unused) {
            $content[] = file_get_contents(
                str_contains($file, '.css?') ? substr($file, 0, strrpos($file, '?')) : $file
            );
        }

        if (\count($content) === 0) {
            return;
        }

        $this->createAssetsDirectory($path);

        file_put_contents($path, $this->minifier->run(implode('', $content)), LOCK_EX);
    }

    private function buildAssetPath(): string
    {
        return $this->appPath->getUploadsDir() . 'assets/' . md5(implode('', array_keys($this->cssRendererStrategy->getFiles()))) . '.css';
    }

    private function createAssetsDirectory(string $path): void
    {
        $concurrentDirectory = \dirname($path);
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
            $this->cssRendererStrategy->initialize();

            $cacheItem->set($this->cssRendererStrategy->getFiles());
            $this->coreCachePool->saveDeferred($cacheItem);
        } else {
            $this->addFiles(array_keys($cacheItem->get()));
        }
    }

    private function buildCacheId(): string
    {
        return 'assets_' . $this->generateFilenameHash();
    }

    public function addFiles(array $files): void
    {
        $this->cssRendererStrategy->addFiles($files);
    }

    public function getFiles(): array
    {
        return $this->cssRendererStrategy->getFiles();
    }
}
