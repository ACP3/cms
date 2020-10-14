<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

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
        $data = [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'foo.css',
            'js' => 'bar.js',
        ];
        $this->libraries->addLibrary('foobar', $data);

        Assert::assertArraySubset(['foobar' => $data], $this->libraries->getLibraries());
    }

    public function testEnableLibraries()
    {
        $this->testAddLibrary();

        $this->libraries->enableLibraries(['foobar']);

        self::assertEquals(['polyfill', 'jquery', 'ajax-form', 'font-awesome', 'foobar'], $this->libraries->getEnabledLibraries());
    }
}
