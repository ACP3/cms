<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Assets;

use ACP3\Core\Assets\Libraries;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LibrariesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Libraries
     */
    private $libraries;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcherMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    protected function setUp()
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

        $this->assertArraySubset(['foobar' => $data], $this->libraries->getLibraries());
    }

    public function testEnableLibraries()
    {
        $this->testAddLibrary();

        $this->libraries->enableLibraries(['foobar']);

        $this->assertEquals(['jquery', 'ajax-form', 'foobar'], $this->libraries->getEnabledLibraries());
    }
}
