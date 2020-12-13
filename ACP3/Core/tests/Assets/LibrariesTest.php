<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Dto\LibraryDto;
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

    public function testAddLibrary()
    {
        $library = new LibraryDto(
            'foobar',
            false,
            false,
            ['jquery'],
            ['foo.css'],
            ['bar.js']
        );
        $this->libraries->addLibrary($library);

        Assert::assertArraySubset(['foobar' => $library], $this->libraries->getLibraries());
    }

    public function testEnableLibraries()
    {
        $this->testAddLibrary();

        $this->libraries->enableLibraries(['foobar']);

        $expected = ['polyfill', 'jquery', 'bootstrap', 'ajax-form', 'font-awesome', 'foobar'];
        $actual = $this->libraries->getEnabledLibraries();

        \sort($expected);
        \sort($actual);

        self::assertEquals($expected, $actual);
    }
}
