<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\FileResolver\TemplateFileCheckerStrategy;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileResolverTest extends TestCase
{
    private FileResolver $fileResolver;

    private ThemePathInterface&MockObject $themeMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->fileResolver = new FileResolver(
            new ApplicationPath(ApplicationMode::DEVELOPMENT),
            $this->themeMock
        );
        $this->fileResolver->addStrategy(new TemplateFileCheckerStrategy());
    }

    private function setUpMockObjects(): void
    {
        $this->themeMock = $this->createMock(ThemePathInterface::class);
    }

    public function testResolveTemplatePath(): void
    {
        $this->setUpThemeMockExpectations(
            ['acp3'],
            [ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3']
        );
        $this->themeMock
            ->method('getThemeDependencies')
            ->willReturnCallback(fn (string $themeName) => match ([$themeName]) {
                ['acp3'] => ['acp3'],
                default => throw new \InvalidArgumentException(),
            });

        $expected = ACP3_ROOT_DIR . '/ACP3/Modules/ACP3/System/Resources/View/Partials/breadcrumb.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/breadcrumb.tpl');
        $this->assertSamePath($expected, $actual);
    }

    private function assertSamePath(string $expected, string $actual): void
    {
        self::assertEquals(
            str_replace('\\', '/', $expected),
            str_replace('\\', '/', $actual)
        );
    }

    public function testResolveTemplatePathWithInheritance(): void
    {
        $this->setUpThemeMockExpectations(
            ['acp3'],
            [ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3']
        );
        $this->themeMock
            ->method('getThemeDependencies')
            ->willReturnCallback(fn (string $themeName) => match ([$themeName]) {
                ['acp3'] => ['acp3'],
                default => throw new \InvalidArgumentException(),
            });

        $expected = ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3/System/Resources/View/Partials/mark.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/mark.tpl');
        $this->assertSamePath($expected, $actual);
    }

    public function testThrowsExceptionIfNoModuleNameProvidedInPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->setUpThemeMockExpectations(
            ['acp3-inherit'],
            [ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3-inherit', ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3', ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3-inherit']
        );
        $this->themeMock
            ->method('getThemeDependencies')
            ->willReturnCallback(fn (string $themeName) => match ([$themeName]) {
                ['acp3-inherit'] => ['acp3-inherit', 'acp3'],
                default => throw new \InvalidArgumentException(),
            });

        $this->fileResolver->resolveTemplatePath('layout.tpl');
    }

    public function testResolveTemplatePathWithMultipleInheritance(): void
    {
        $this->setUpThemeMockExpectations(
            ['acp3-inherit', 'acp3'],
            [ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3-inherit', ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3', ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3-inherit']
        );
        $this->themeMock
            ->method('getThemeDependencies')
            ->willReturnCallback(fn (string $themeName) => match ([$themeName]) {
                ['acp3-inherit'] => ['acp3-inherit', 'acp3'],
                ['acp3'] => ['acp3'],
                default => throw new \InvalidArgumentException(),
            });

        $expected = ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3/System/Resources/View/layout.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/layout.tpl');
        $this->assertSamePath($expected, $actual);
    }

    /**
     * @param string[] $currentThemeCalls
     * @param string[] $designPathInternalCalls
     */
    private function setUpThemeMockExpectations(array $currentThemeCalls, array $designPathInternalCalls): void
    {
        $this->themeMock
            ->method('getCurrentTheme')
            ->willReturnOnConsecutiveCalls(...$currentThemeCalls);
        $this->themeMock
            ->method('getDesignPathInternal')
            ->willReturnOnConsecutiveCalls(...$designPathInternalCalls);
    }

    public function testResolveTemplatePathWithDeeplyNestedFolderStructure(): void
    {
        $this->setUpThemeMockExpectations(
            ['acp3-inherit'],
            [ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3-inherit', ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3']
        );
        $this->themeMock
            ->method('getThemeDependencies')
            ->willReturnCallback(fn (string $themeName) => match ([$themeName]) {
                ['acp3-inherit'] => ['acp3-inherit', 'acp3'],
                default => throw new \InvalidArgumentException(),
            });

        $expected = ACP3_ROOT_DIR . '/ACP3/Core/fixtures/designs/acp3-inherit/System/Resources/View/Partials/Foo/bar/baz.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/Foo/bar/baz.tpl');
        $this->assertSamePath($expected, $actual);
    }
}
