<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Assets\Libraries;
use ACP3\Core\Modules;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CSSRendererStrategyTest extends TestCase
{
    private Assets&MockObject $assetsMock;
    private Libraries&MockObject $librariesMock;
    private Modules&MockObject $modulesMock;
    private FileResolver&MockObject $fileResolverMock;
    private CSSRendererStrategy $CSSRendererStrategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetsMock = $this->createMock(Assets::class);
        $this->librariesMock = $this->createMock(Libraries::class);
        $this->modulesMock = $this->createMock(Modules::class);
        $this->fileResolverMock = $this->createMock(FileResolver::class);

        $this->CSSRendererStrategy = new CSSRendererStrategy(
            $this->assetsMock,
            $this->librariesMock,
            $this->modulesMock,
            $this->fileResolverMock
        );
    }

    public function testRenderHtmlElementWithoutAnyStylesheets(): void
    {
        self::assertEquals('', $this->CSSRendererStrategy->renderHtmlElement());
    }

    public function testRenderHtmlElementWithStylesheets(): void
    {
        $this->librariesMock->method('getEnabledLibraries')
            ->willReturn([new LibraryEntity('foo', false, [], ['foo.css'], [], 'system')]);

        $this->fileResolverMock->method('getWebStaticAssetPath')
            ->withConsecutive(['system', 'Assets/css', 'foo.css'], ['System', 'Assets/css', 'layout.css'])
            ->willReturnOnConsecutiveCalls('/ACP3/Modules/ACP3/System/Resources/Assets/css/foo.css', '');

        self::assertStringContainsString('<link rel="stylesheet" type="text/css" href="/ACP3/Modules/ACP3/System/Resources/Assets/css/foo.css', $this->CSSRendererStrategy->renderHtmlElement());
    }
}
