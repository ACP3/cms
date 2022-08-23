<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Helpers\View\Exception\SvgIconNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IconTest extends TestCase
{
    private FileResolver&MockObject $fileResolverMock;
    private Icon $iconHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileResolverMock = $this->createMock(FileResolver::class);

        $this->iconHelper = new Icon($this->fileResolverMock);
    }

    public function testInvoke(): void
    {
        $this->fileResolverMock->expects($this->once())
            ->method('getStaticAssetPath')
            ->with('system', 'Assets/svgs/solid', 'spinner.svg')
            ->willReturn(\dirname(__DIR__, 4) . '/Modules/ACP3/System/Resources/Assets/svgs/solid/spinner.svg');

        self::assertStringContainsString('<svg class="svg-icon svg-icon__spinner"', ($this->iconHelper)('solid', 'spinner'));
    }

    public function testInvokeWithCssSelectors(): void
    {
        $this->fileResolverMock->expects($this->once())
            ->method('getStaticAssetPath')
            ->with('system', 'Assets/svgs/solid', 'spinner.svg')
            ->willReturn(\dirname(__DIR__, 4) . '/Modules/ACP3/System/Resources/Assets/svgs/solid/spinner.svg');

        $actual = ($this->iconHelper)('solid', 'spinner', ['cssSelectors' => 'my-awesome-css-selector']);
        self::assertStringContainsString('<svg class="svg-icon svg-icon__spinner my-awesome-css-selector"', $actual);
        self::assertStringNotContainsString('<title>', $actual);
    }

    public function testInvokeWithTitle(): void
    {
        $this->fileResolverMock->expects($this->once())
            ->method('getStaticAssetPath')
            ->with('system', 'Assets/svgs/solid', 'spinner.svg')
            ->willReturn(\dirname(__DIR__, 4) . '/Modules/ACP3/System/Resources/Assets/svgs/solid/spinner.svg');

        self::assertStringContainsString('<title>My title</title><path', ($this->iconHelper)('solid', 'spinner', ['title' => 'My title']));
    }

    public function testThrowsExceptionOnInvalidFile(): void
    {
        $this->expectException(SvgIconNotFoundException::class);

        $this->fileResolverMock->expects($this->once())
            ->method('getStaticAssetPath')
            ->with('system', 'Assets/svgs/solid', 'invalid-file.svg')
            ->willReturn('');

        ($this->iconHelper)('solid', 'invalid-file');
    }
}
