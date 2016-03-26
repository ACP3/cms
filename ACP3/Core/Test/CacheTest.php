<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test;


use ACP3\Core\Cache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Cache
     */
    private $cache;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheDriverFactoryMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->cache = new Cache(
            $this->cacheDriverFactoryMock,
            'test'
        );
    }

    private function initializeMockObjects()
    {
        $this->cacheDriverFactoryMock = $this->getMockBuilder(Cache\CacheDriverFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
    }

    public function testGetDriverInstanceOf()
    {
        $this->setUpCacheDriverFactoryMockExpectations('test');
        $this->assertInstanceOf(CacheProvider::class, $this->cache->getDriver());
    }

    /**
     * @param string $namespace
     */
    private function setUpCacheDriverFactoryMockExpectations($namespace)
    {
        $this->cacheDriverFactoryMock->expects($this->once())
            ->method('create')
            ->with($namespace)
            ->willReturn(new ArrayCache());
    }
}
