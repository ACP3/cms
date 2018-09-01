<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Assets;

use ACP3\Core\Assets\Cache;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\Theme;
use ACP3\Core\Modules;

class FileResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $assetsCache;
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $themeMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $modulesMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->appPath
            ->setDesignRootPathInternal(ACP3_ROOT_DIR . 'tests/designs/');

        $this->fileResolver = new FileResolver(
            $this->assetsCache,
            $this->appPath,
            $this->themeMock,
            $this->modulesMock
        );
    }

    private function setUpMockObjects()
    {
        $this->assetsCache = $this->createMock(Cache::class);
        $this->themeMock = $this->createMock(Theme::class);
        $this->modulesMock = $this->createMock(Modules::class);
    }

    public function testResolveTemplatePath()
    {
        $this->setUpThemeMockExpectations('acp3', ['acp3']);

        $this->modulesMock->expects($this->any())
            ->method('getModuleInfo')
            ->with('System')
            ->willReturn(['vendor' => 'ACP3']);

        $expected = $this->appPath->getModulesDir() . 'ACP3/System/Resources/View/Partials/breadcrumb.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/breadcrumb.tpl');
        $this->assertEquals($expected, $actual);
    }

    public function testResolveTemplatePathWithInheritance()
    {
        $this->setUpThemeMockExpectations('acp3', ['acp3']);

        $this->modulesMock->expects($this->any())
            ->method('getModuleInfo')
            ->with('System')
            ->willReturn(['vendor' => 'ACP3']);

        $expected = $this->appPath->getDesignRootPathInternal() . 'acp3/System/View/Partials/mark.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/mark.tpl');
        $this->assertEquals($expected, $actual);
    }

    public function testResolveTemplatePathWithMultipleInheritance()
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn('acp3-inherit');
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->willReturnOnConsecutiveCalls(['acp3-inherit', 'acp3'], ['acp3']);

        $this->modulesMock->expects($this->any())
            ->method('getModuleInfo')
            ->with('')
            ->willReturn([]);

        $expected = ACP3_ROOT_DIR . 'tests/designs/acp3/layout.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('layout.tpl');
        $this->assertEquals($expected, $actual);
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

    public function testResolveTemplatePathWithDeeplyNestedFolderStructure()
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn('acp3-inherit');
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->willReturnOnConsecutiveCalls(['acp3-inherit', 'acp3'], ['acp3']);

        $this->modulesMock->expects($this->any())
            ->method('getModuleInfo')
            ->with('System')
            ->willReturn(['vendor' => 'ACP3']);

        $expected = ACP3_ROOT_DIR . 'tests/designs/acp3-inherit/System/View/Partials/Foo/bar/baz.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/Foo/bar/baz.tpl');
        $this->assertEquals($expected, $actual);
    }
}
