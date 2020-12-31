<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Assets\Entity\LibraryEntity;
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
     * @var \PHPUnit\Framework\MockObject\MockObject & EventDispatcherInterface
     */
    private $eventDispatcherMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & RequestInterface
     */
    private $requestMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $theme = $this->createMock(ThemePathInterface::class);
        $theme->expects(self::once())
            ->method('getDesignPathInternal')
            ->willReturn(ACP3_ROOT_DIR . '/tests/designs/acp3/');
        $libraries = new Assets\Libraries($this->requestMock, $this->eventDispatcherMock);
        $libraries->addLibrary(new LibraryEntity('bootstrap'));

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

        $expected = \explode(',', 'bootstrap');
        $actual = \explode(',', $libraries);

        \sort($expected);
        \sort($actual);

        self::assertEquals($expected, $actual);
    }

    public function testFetchAdditionalThemeCssFiles()
    {
        $files = $this->assets->fetchAdditionalThemeCssFiles();

        self::assertEquals(['additional-style.css'], $files);
    }

    public function testFetchAdditionalThemeJsFiles()
    {
        $files = $this->assets->fetchAdditionalThemeJsFiles();

        self::assertEquals(['additional-script.js'], $files);
    }
}
