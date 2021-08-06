<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Entity\LibraryEntity;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LibrariesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Libraries
     */
    private $libraries;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & RequestStack
     */
    private $requestStackMock;
    /**
     * @var \ACP3\Core\Assets\LibrariesCache & \PHPUnit\Framework\MockObject\MockObject
     */
    private $librariesCacheMock;

    protected function setup(): void
    {
        $this->requestStackMock = $this->createMock(RequestStack::class);
        $this->librariesCacheMock = $this->createMock(LibrariesCache::class);

        $this->libraries = new Libraries($this->requestStackMock, $this->librariesCacheMock);
    }

    private function configureRequestStackMock(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->requestStackMock->expects(self::atLeastOnce())
            ->method('getMasterRequest')
            ->willReturn($requestMock);
        $this->requestStackMock->expects(self::atLeastOnce())
            ->method('getCurrentRequest')
            ->willReturn($requestMock);
    }

    public function testAddLibrary(): void
    {
        $this->libraries->addLibrary(new LibraryEntity('jquery', false, [], [], [], 'system'));

        $library = new LibraryEntity(
            'foobar',
            false,
            ['jquery'],
            ['foo.css'],
            ['bar.js'],
            'system'
        );
        $this->libraries->addLibrary($library);

        Assert::assertArraySubset(['foobar' => $library], $this->libraries->getLibraries());
    }

    public function testEnableLibraries(): void
    {
        $this->configureRequestStackMock();

        $this->testAddLibrary();

        $this->libraries->enableLibraries(['foobar']);

        $expected = ['jquery', 'foobar'];
        $actual = array_keys($this->libraries->getEnabledLibraries());

        sort($expected);
        sort($actual);

        self::assertEquals($expected, $actual);
    }

    public function testThrowsExceptionWhenEnablingUnregisteredLibrary(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->libraries->enableLibraries(['foobar']);
    }

    public function testGetEnabledLibrariesAsString(): void
    {
        $this->configureRequestStackMock();

        self::assertEquals('', $this->libraries->getEnabledLibrariesAsString());

        $this->libraries->addLibrary(new LibraryEntity('foobar', false, [], ['foo.css'], [], 'testModule'));
        $this->libraries->enableLibraries(['foobar']);

        self::assertEquals('foobar', $this->libraries->getEnabledLibrariesAsString());

        $this->libraries->addLibrary(new LibraryEntity('baz', false, [], ['baz.css'], [], 'testModule'));
        $this->libraries->enableLibraries(['baz']);

        self::assertEquals('foobar,baz', $this->libraries->getEnabledLibrariesAsString());
    }
}
