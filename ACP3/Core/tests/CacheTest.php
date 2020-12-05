<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ACP3\Core\Cache
     */
    private $cache;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheDriverFactoryMock;

    protected function setup(): void
    {
        $this->initializeMockObjects();

        $this->cache = new Cache(
            $this->cacheDriverFactoryMock,
            'test'
        );
    }

    private function initializeMockObjects()
    {
        $this->cacheDriverFactoryMock = $this->createMock(Cache\CacheDriverFactory::class);
    }

    public function testGetDriverInstanceOf()
    {
        $this->setUpCacheDriverFactoryMockExpectations('test');
        self::assertInstanceOf(CacheProvider::class, $this->cache->getDriver());
    }

    /**
     * @param string $namespace
     */
    private function setUpCacheDriverFactoryMockExpectations($namespace)
    {
        $this->cacheDriverFactoryMock->expects(self::once())
            ->method('create')
            ->with($namespace)
            ->willReturn(new ArrayCache());
    }
}
