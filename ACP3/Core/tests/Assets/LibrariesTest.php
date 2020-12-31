<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Http\RequestInterface;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LibrariesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Libraries
     */
    private $libraries;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $eventDispatcherMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    protected function setup(): void
    {
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);

        $this->libraries = new Libraries($this->requestMock, $this->eventDispatcherMock);
    }

    public function testAddLibrary(): void
    {
        $this->libraries->addLibrary(new LibraryEntity('jquery'));

        $library = new LibraryEntity(
            'foobar',
            false,
            ['jquery'],
            ['foo.css'],
            ['bar.js']
        );
        $this->libraries->addLibrary($library);

        Assert::assertArraySubset(['foobar' => $library], $this->libraries->getLibraries());
    }

    public function testEnableLibraries(): void
    {
        $this->testAddLibrary();

        $this->libraries->enableLibraries(['foobar']);

        $expected = ['jquery', 'foobar'];
        $actual = $this->libraries->getEnabledLibraries();

        \sort($expected);
        \sort($actual);

        self::assertEquals($expected, $actual);
    }

    public function testThrowsExceptionWhenEnablingUnregisteredLibrary(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->libraries->enableLibraries(['foobar']);
    }
}
