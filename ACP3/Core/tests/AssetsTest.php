<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Assets\Libraries;
use ACP3\Core\Environment\ThemePathInterface;

class AssetsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Assets
     */
    private $assets;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $theme;
    /**
     * @var \ACP3\Core\Assets\Libraries&\PHPUnit\Framework\MockObject\MockObject
     */
    private $librariesMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->librariesMock->addLibrary(new LibraryEntity('bootstrap', false, [], [], [], 'system'));

        $this->assets = new Assets($this->theme, $this->librariesMock);
    }

    private function setUpMockObjects(): void
    {
        $this->theme = $this->createMock(ThemePathInterface::class);
        $this->librariesMock = $this->createMock(Libraries::class);
    }

    private function configureThemeMock(): void
    {
        $this->theme->expects(self::once())
            ->method('getCurrentThemeInfo')
            ->willReturn(['libraries' => [], 'css' => ['additional-style.css'], 'js' => ['additional-script.js']]);
    }

    public function testFetchAdditionalThemeCssFiles(): void
    {
        $this->configureThemeMock();

        $this->assets->initializeTheme();
        $files = $this->assets->fetchAdditionalThemeCssFiles();

        self::assertEquals(['additional-style.css'], $files);
    }

    public function testFetchAdditionalThemeCssFilesThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->assets->fetchAdditionalThemeCssFiles();
    }

    public function testFetchAdditionalThemeJsFiles(): void
    {
        $this->configureThemeMock();

        $this->assets->initializeTheme();
        $files = $this->assets->fetchAdditionalThemeJsFiles();

        self::assertEquals(['additional-script.js'], $files);
    }

    public function testFetchAdditionalThemeJsFilesThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->assets->fetchAdditionalThemeJsFiles();
    }
}
