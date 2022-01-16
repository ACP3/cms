<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\CacheItem;

class FileResolverTest extends TestCase
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CacheItemPoolInterface
     */
    private $assetsCachePoolMock;
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\ACP3\Core\Environment\ThemePathInterface
     */
    private $themeMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);

        $this->fileResolver = new FileResolver(
            $this->assetsCachePoolMock,
            $this->appPath,
            $this->themeMock
        );
    }

    private function setUpMockObjects(): void
    {
        $this->assetsCachePoolMock = $this->createMock(CacheItemPoolInterface::class);
        $this->themeMock = $this->createMock(ThemePathInterface::class);

        $this->assetsCachePoolMock
            ->method('getItem')
            ->willReturn(new CacheItem());
    }

    public function testResolveTemplatePath(): void
    {
        $this->setUpThemeMockExpectations('acp3', [['acp3']], [ACP3_ROOT_DIR . '/tests/designs/acp3']);

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
        $this->setUpThemeMockExpectations('acp3', [['acp3']], [ACP3_ROOT_DIR . '/tests/designs/acp3']);

        $expected = ACP3_ROOT_DIR . '/tests/designs/acp3/System/View/Partials/mark.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/mark.tpl');
        $this->assertSamePath($expected, $actual);
    }

    public function testThrowsExceptionIfNoModuleNameProvidedInPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->setUpThemeMockExpectations(
            'acp3-inherit',
            [['acp3-inherit', 'acp3'], ['acp3']],
            [ACP3_ROOT_DIR . '/tests/designs/acp3-inherit', ACP3_ROOT_DIR . '/tests/designs/acp3', ACP3_ROOT_DIR . '/tests/designs/acp3-inherit']
        );

        $this->fileResolver->resolveTemplatePath('layout.tpl');
    }

    public function testResolveTemplatePathWithMultipleInheritance(): void
    {
        $this->setUpThemeMockExpectations(
            'acp3-inherit',
            [['acp3-inherit', 'acp3'], ['acp3']],
            [ACP3_ROOT_DIR . '/tests/designs/acp3-inherit', ACP3_ROOT_DIR . '/tests/designs/acp3', ACP3_ROOT_DIR . '/tests/designs/acp3-inherit']
        );

        $expected = ACP3_ROOT_DIR . '/tests/designs/acp3/System/View/layout.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/layout.tpl');
        $this->assertSamePath($expected, $actual);
    }

    /**
     * @param string[][] $themeDependencies
     * @param string[]   $designPathInternal
     */
    private function setUpThemeMockExpectations(string $themeName, array $themeDependencies, array $designPathInternal): void
    {
        $this->themeMock
            ->method('getCurrentTheme')
            ->willReturn($themeName);
        $this->themeMock
            ->method('getDesignPathInternal')
            ->willReturnOnConsecutiveCalls(...$designPathInternal);
        $this->themeMock
            ->method('getThemeDependencies')
            ->with($themeName)
            ->willReturnOnConsecutiveCalls(...$themeDependencies);
    }

    public function testResolveTemplatePathWithDeeplyNestedFolderStructure(): void
    {
        $this->setUpThemeMockExpectations(
            'acp3-inherit',
            [['acp3-inherit', 'acp3'], ['acp3']],
            [ACP3_ROOT_DIR . '/tests/designs/acp3-inherit', ACP3_ROOT_DIR . '/tests/designs/acp3']
        );

        $expected = ACP3_ROOT_DIR . '/tests/designs/acp3-inherit/System/View/Partials/Foo/bar/baz.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/Foo/bar/baz.tpl');
        $this->assertSamePath($expected, $actual);
    }
}
