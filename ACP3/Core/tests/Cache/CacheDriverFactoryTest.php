<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\PhpFileCache;

class CacheDriverFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & ApplicationPath
     */
    private $appPathMock;
    /**
     * @var \ACP3\Core\Cache\CacheDriverFactory
     */
    private $cacheDriverFactory;

    protected function setup(): void
    {
        $this->initializeMockObjects();
    }

    private function initializeMockObjects(): void
    {
        $this->appPathMock = $this->createMock(ApplicationPath::class);
    }

    private function initializeCacheDriverFactory($cacheDriver, $environment): void
    {
        $this->cacheDriverFactory = new CacheDriverFactory(
            $this->appPathMock,
            $cacheDriver,
            $environment
        );
    }

    public function testCreateWithValidCacheDriver(): void
    {
        $this->setUpAppPathMockExpectations();

        $this->initializeCacheDriverFactory('Array', 'test');

        self::assertInstanceOf(CacheProvider::class, $this->cacheDriverFactory->create('test'));
    }

    private function setUpAppPathMockExpectations(): void
    {
        $this->appPathMock->expects(self::any())
            ->method('getCacheDir')
            ->willReturn('cache/');
    }

    public function testCreateInvalidCacheDriverThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->setUpAppPathMockExpectations();

        $this->initializeCacheDriverFactory('LoremIpsum', 'test');

        $this->cacheDriverFactory->create('test');
    }

    public function testCreateForceArrayCacheForDeveloperMode(): void
    {
        $this->setUpAppPathMockExpectations();

        $this->initializeCacheDriverFactory('PhpFile', ApplicationMode::DEVELOPMENT);

        self::assertInstanceOf(ArrayCache::class, $this->cacheDriverFactory->create('test'));
    }

    public function testCreateWithPhpFileCacheDriver(): void
    {
        $this->setUpAppPathMockExpectations();

        $this->initializeCacheDriverFactory('PhpFile', 'test');

        self::assertInstanceOf(PhpFileCache::class, $this->cacheDriverFactory->create('test'));
    }
}
