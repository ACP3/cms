<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CachingJavaScriptRendererStrategy implements JavaScriptRendererStrategyInterface
{
    public function __construct(
        private readonly JavaScriptRendererStrategy $javaScriptRendererStrategy,
        private readonly RequestInterface $request,
        private readonly UserModelInterface $userModel,
        private readonly ThemePathInterface $themePath,
        private readonly CacheItemPoolInterface $coreCachePool,
    ) {
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
            $this->javaScriptRendererStrategy->initialize();

            $cacheItem->set($this->javaScriptRendererStrategy->getFiles());
            $this->coreCachePool->saveDeferred($cacheItem);
        } else {
            $this->addFiles(array_keys($cacheItem->get()));
        }
    }

    private function buildCacheId(): string
    {
        return 'assets_' . $this->generateFilenameHash();
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
                'js',
            ]
        ));
    }

    public function renderHtmlElement(): string
    {
        return $this->javaScriptRendererStrategy->renderHtmlElement();
    }

    public function addFiles(array $files): void
    {
        $this->javaScriptRendererStrategy->addFiles($files);
    }

    public function getFiles(): array
    {
        return $this->javaScriptRendererStrategy->getFiles();
    }
}
