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
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CSSRendererStrategyTest extends TestCase
{
    private RequestInterface&MockObject $requestMock;
    private Assets&MockObject $assetsMock;
    private Libraries&MockObject $librariesMock;
    private Modules&MockObject $modulesMock;
    private FileResolver&MockObject $fileResolverMock;
    private CSSRendererStrategy $CSSRendererStrategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->assetsMock = $this->createMock(Assets::class);
        $this->librariesMock = $this->createMock(Libraries::class);
        $this->modulesMock = $this->createMock(Modules::class);
        $this->fileResolverMock = $this->createMock(FileResolver::class);

        $this->CSSRendererStrategy = new CSSRendererStrategy(
            $this->requestMock,
            $this->assetsMock,
            $this->librariesMock,
            $this->modulesMock,
            $this->fileResolverMock
        );
    }

    public function testRenderHtmlElementWithoutAnyStylesheets(): void
    {
        $this->requestMock->method('getArea')
            ->willReturn(AreaEnum::AREA_FRONTEND);

        self::assertEquals('', $this->CSSRendererStrategy->renderHtmlElement());
    }

    public function testRenderHtmlElementWithStylesheets(): void
    {
        $this->requestMock->method('getArea')
            ->willReturn(AreaEnum::AREA_FRONTEND);

        $this->librariesMock->method('getEnabledLibraries')
            ->willReturn([new LibraryEntity('foo', false, [], ['foo.css'], [], 'system')]);

        $this->fileResolverMock->method('getWebStaticAssetPath')
            ->willReturnCallback(fn (string $moduleName, string $resourceDirectory, string $file) => match ([$moduleName, $resourceDirectory, $file]) {
                ['system', 'Assets/css', 'foo.css'] => '/ACP3/Modules/ACP3/System/Resources/Assets/css/foo.css',
                ['System', 'Assets/css', 'layout.css'] => '',
                default => throw new \InvalidArgumentException(),
            });

        self::assertStringContainsString('<link rel="stylesheet" type="text/css" href="/ACP3/Modules/ACP3/System/Resources/Assets/css/foo.css', $this->CSSRendererStrategy->renderHtmlElement());
    }
}
