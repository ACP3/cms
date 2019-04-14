<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test;

use ACP3\Core\Assets;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AssetsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Assets
     */
    private $assets;
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
        $this->setUpMockObjects();

        $theme = $this->createMock(ThemePathInterface::class);
        $theme->expects($this->once())
            ->method('getDesignPathInternal')
            ->willReturn(ACP3_ROOT_DIR . 'tests/designs/acp3/');
        $libraries = new Assets\Libraries($this->requestMock, $this->eventDispatcherMock);

        $this->assets = new Assets($theme, $libraries);
    }

    private function setUpMockObjects()
    {
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    public function testDefaultLibrariesEnabled()
    {
        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('polyfill,jquery,bootstrap,ajax-form', $libraries);
    }

    public function testEnableDatepicker()
    {
        $this->assets->enableLibraries(['datetimepicker']);

        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('polyfill,jquery,bootstrap,ajax-form,moment,datetimepicker', $libraries);
    }

    public function testFetchAdditionalThemeCssFiles()
    {
        $files = $this->assets->fetchAdditionalThemeCssFiles();

        $this->assertEquals(['additional-style.css'], $files);
    }

    public function testFetchAdditionalThemeJsFiles()
    {
        $files = $this->assets->fetchAdditionalThemeJsFiles();

        $this->assertEquals(['additional-script.js'], $files);
    }
}
