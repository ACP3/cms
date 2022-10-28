<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View\Renderer\Smarty\AbstractPluginTest;
use PHPUnit\Framework\MockObject\MockObject;

class PageCssClassesTest extends AbstractPluginTest
{
    /**
     * @var PageCssClasses
     */
    protected $plugin;
    private MockObject|\ACP3\Core\Assets\PageCssClasses $pageCssClassesMock;
    private RequestInterface|MockObject $requestMock;
    private \Smarty_Internal_Template|MockObject $smartyInternalTemplateMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->plugin = new PageCssClasses(
            $this->pageCssClassesMock,
            $this->requestMock
        );
    }

    private function setUpMockObjects(): void
    {
        $this->pageCssClassesMock = $this->createMock(\ACP3\Core\Assets\PageCssClasses::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->smartyInternalTemplateMock = $this->createMock(\Smarty_Internal_Template::class);
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess(string $expected, string $templateOutput, int $detailsCalls, bool $isHomepage, AreaEnum $area): void
    {
        $this->setUpPageCssClassesMockExpectations($detailsCalls);
        $this->setUpRequestMockExpectations($isHomepage, $area);

        self::assertStringContainsString(
            $expected,
            $this->plugin->__invoke($templateOutput, $this->smartyInternalTemplateMock)
        );
    }

    /**
     * @return array<string, array{string, string, int, bool, AreaEnum}>
     */
    public function processDataProvider(): array
    {
        return [
            'in-frontend' => [
                '<body class="foo foo-bar-baz foo-bar-pagetitle">',
                $this->getTemplateOutput(),
                1,
                false,
                AreaEnum::AREA_FRONTEND,
            ],
            'is-homepage' => [
                '<body class="foo foo-bar-baz is-homepage">',
                $this->getTemplateOutput(),
                0,
                true,
                AreaEnum::AREA_FRONTEND,
            ],
            'in-admin' => [
                '<body class="foo foo-bar-baz in-admin">',
                $this->getTemplateOutput(),
                0,
                false,
                AreaEnum::AREA_ADMIN,
            ],
            'with-existing-class-attribute' => [
                '<body class="already-exists foo foo-bar-baz foo-bar-pagetitle">',
                $this->getTemplateOutputWithExistingClassAttribute(),
                1,
                false,
                AreaEnum::AREA_FRONTEND,
            ],
        ];
    }

    public function testProcessWithNoHtmlBodyTag(): void
    {
        $templateContent = <<<HTML
<p>Baz</p>
HTML;

        self::assertEquals(
            $templateContent,
            $this->plugin->__invoke($templateContent, $this->smartyInternalTemplateMock)
        );
    }

    private function setUpPageCssClassesMockExpectations(int $getDetailsCalls): void
    {
        $this->pageCssClassesMock
            ->expects(self::once())
            ->method('getModule')
            ->willReturn('foo');
        $this->pageCssClassesMock
            ->expects(self::once())
            ->method('getControllerAction')
            ->willReturn('foo-bar-baz');
        $this->pageCssClassesMock
            ->expects(self::exactly($getDetailsCalls))
            ->method('getDetails')
            ->willReturn('foo-bar-pagetitle');
    }

    private function setUpRequestMockExpectations(bool $isHomepage, AreaEnum $area): void
    {
        $this->requestMock->expects($area === AreaEnum::AREA_FRONTEND ? self::once() : $this->never())
            ->method('isHomepage')
            ->willReturn($isHomepage);
        $this->requestMock->expects(self::once())
            ->method('getArea')
            ->willReturn($area);
    }

    private function getTemplateOutput(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>Foobar</title>
</head>
<body>
<p>Baz</p>
</body>
</html>
HTML;
    }

    private function getTemplateOutputWithExistingClassAttribute(): string
    {
        return str_replace($this->getTemplateOutput(), '<body>', '<body class="already-exists">');
    }
}
