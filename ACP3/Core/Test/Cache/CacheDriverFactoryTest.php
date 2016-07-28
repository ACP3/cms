<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Cache;


use ACP3\Core\Cache\CacheDriverFactory;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\PhpFileCache;
use Symfony\Component\DependencyInjection\Container;

class CacheDriverFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $containerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $appPathMock;
    /**
     * @var \ACP3\Core\Cache\CacheDriverFactory
     */
    private $cacheDriverFactory;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->cacheDriverFactory = new CacheDriverFactory(
            $this->containerMock,
            $this->appPathMock
        );
    }

    private function initializeMockObjects()
    {
        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParameter', 'hasParameter'])
            ->getMock();

        $this->appPathMock = $this->getMockBuilder(ApplicationPath::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCacheDir'])
            ->getMock();
    }

    public function testCreateWithValidCacheDriver()
    {
        $this->setUpContainerMockExpectations(ApplicationMode::DEVELOPMENT, 'Array');
        $this->setUpAppPathMockExpectations();

        $this->assertInstanceOf(CacheProvider::class, $this->cacheDriverFactory->create('test'));
    }

    /**
     * @param string $environment
     * @param string $cacheDriverName
     */
    private function setUpContainerMockExpectations($environment, $cacheDriverName)
    {
        $this->containerMock->expects($this->once())
            ->method('hasParameter')
            ->with('cache_driver')
            ->willReturn(true);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('getParameter')
            ->withConsecutive(['core.environment'], ['cache_driver'])
            ->willReturnOnConsecutiveCalls($environment, $cacheDriverName);
    }

    private function setUpAppPathMockExpectations()
    {
        $this->appPathMock->expects($this->any())
            ->method('getCacheDir')
            ->willReturn('cache/');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidCacheDriverThrowsException()
    {
        $this->setUpContainerMockExpectations('test', 'LoremIpsum');
        $this->setUpAppPathMockExpectations();

        echo get_class($this->cacheDriverFactory->create('test'));
    }

    public function testCreateForceArrayCacheForDeveloperMode()
    {
        $this->setUpContainerMockExpectations(ApplicationMode::DEVELOPMENT, 'PhpFile');
        $this->setUpAppPathMockExpectations();

        $this->assertInstanceOf(ArrayCache::class, $this->cacheDriverFactory->create('test'));
    }

    public function testCreateWithPhpFileCacheDriver()
    {
        $this->setUpContainerMockExpectations('test', 'PhpFile');
        $this->setUpAppPathMockExpectations();

        $this->assertInstanceOf(PhpFileCache::class, $this->cacheDriverFactory->create('test'));
    }
}
