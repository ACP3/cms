<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test;

use ACP3\Core\Assets;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AssetsTest extends \PHPUnit_Framework_TestCase
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

        $appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $appPath
            ->setDesignRootPathInternal(ACP3_ROOT_DIR . 'tests/designs/')
            ->setDesignPathInternal('acp3/');
        $libraries = new Assets\Libraries($this->requestMock, $this->eventDispatcherMock);

        $this->assets = new Assets($appPath, $libraries);
    }

    private function setUpMockObjects()
    {
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->setMethods([
                'dispatch',
                'addListener',
                'addSubscriber',
                'removeListener',
                'removeSubscriber',
                'getListeners',
                'getListenerPriority',
                'hasListeners',
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDefaultLibrariesEnabled()
    {
        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('jquery,ajax-form,bootstrap', $libraries);
    }

    public function testEnableDatepicker()
    {
        $this->assets->enableLibraries(['datetimepicker']);

        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('moment,jquery,ajax-form,bootstrap,datetimepicker', $libraries);
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
