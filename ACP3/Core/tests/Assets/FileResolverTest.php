<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\Theme;
use PHPUnit\Framework\TestCase;

class FileResolverTest extends TestCase
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $assetsCache;
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $themeMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->appPath
            ->setDesignRootPathInternal(ACP3_ROOT_DIR . '/tests/designs/');

        $this->fileResolver = new FileResolver(
            $this->assetsCache,
            $this->appPath,
            $this->themeMock
        );
    }

    private function setUpMockObjects(): void
    {
        $this->assetsCache = $this->createMock(Cache::class);
        $this->themeMock = $this->createMock(Theme::class);
    }

    public function testResolveTemplatePath(): void
    {
        $this->setUpThemeMockExpectations('acp3', ['acp3']);

        $expected = ACP3_ROOT_DIR . '/ACP3/Modules/ACP3/System/Resources/View/Partials/breadcrumb.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/breadcrumb.tpl');
        self::assertSamePath($expected, $actual);
    }

    private function assertSamePath(string $expected, string $actual): void
    {
        self::assertEquals(
            \str_replace('\\', '/', $expected),
            \str_replace('\\', '/', $actual)
        );
    }

    public function testResolveTemplatePathWithInheritance(): void
    {
        $this->setUpThemeMockExpectations('acp3', ['acp3']);

        $expected = $this->appPath->getDesignRootPathInternal() . 'acp3/System/View/Partials/mark.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/mark.tpl');
        self::assertSamePath($expected, $actual);
    }

    public function testResolveTemplatePathWithMultipleInheritance(): void
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn('acp3-inherit');
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->willReturnOnConsecutiveCalls(['acp3-inherit', 'acp3'], ['acp3']);

        $expected = ACP3_ROOT_DIR . '/tests/designs/acp3/layout.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('layout.tpl');
        self::assertSamePath($expected, $actual);
    }

    private function setUpThemeMockExpectations(string $themeName, array $dependencies): void
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn($themeName);
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->with($themeName)
            ->willReturn($dependencies);
    }

    public function testResolveTemplatePathWithDeeplyNestedFolderStructure(): void
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn('acp3-inherit');
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->willReturnOnConsecutiveCalls(['acp3-inherit', 'acp3'], ['acp3']);

        $expected = ACP3_ROOT_DIR . '/tests/designs/acp3-inherit/System/View/Partials/Foo/bar/baz.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/Foo/bar/baz.tpl');
        self::assertSamePath($expected, $actual);
    }
}
