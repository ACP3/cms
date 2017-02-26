<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Assets;


use ACP3\Core\Assets\Libraries;
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

    protected function setUp()
    {
        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->libraries = new Libraries($this->eventDispatcherMock);
    }

    public function testAddLibrary()
    {
        $data = [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'foo.css',
            'js' => 'bar.js'
        ];
        $this->libraries->addLibrary('foobar', $data);

        $this->assertArraySubset(['foobar' => $data], $this->libraries->getLibraries());
    }

    public function testEnableLibraries()
    {
        $this->testAddLibrary();

        $this->libraries->enableLibraries(['foobar']);

        $this->assertEquals(['jquery', 'js-cookie', 'foobar'], $this->libraries->getEnabledLibraries());
    }
}
