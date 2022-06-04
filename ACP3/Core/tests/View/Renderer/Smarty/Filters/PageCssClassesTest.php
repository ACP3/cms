<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\Request;
use ACP3\Core\View\Renderer\Smarty\AbstractPluginTest;

class PageCssClassesTest extends AbstractPluginTest
{
    /**
     * @var PageCssClasses
     */
    protected $plugin;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\ACP3\Core\Assets\PageCssClasses
     */
    private $pageCssClassesMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Request
     */
    private $requestMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Smarty_Internal_Template
     */
    private $smartyInternalTemplateMock;

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
        $this->requestMock = $this->createMock(Request::class);
        $this->smartyInternalTemplateMock = $this->createMock(\Smarty_Internal_Template::class);
    }

    public function testProcessInFrontend(): void
    {
        $this->setUpPageCssClassesMockExpectations();
        $this->setUpRequestMockExpectations();

        $expected = <<<HTML
<html>
<head>
<title>Foobar</title>
</head>
<body class="foo foo-bar-baz foo-bar-pagetitle">
<p>Baz</p>
</body>
</html>
HTML;

        self::assertEquals(
            $expected,
            $this->plugin->__invoke($this->getTemplateContent(), $this->smartyInternalTemplateMock)
        );
    }

    private function setUpPageCssClassesMockExpectations(int $getDetailsCalls = 1): void
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

    private function setUpRequestMockExpectations(bool $isHomepage = false, AreaEnum $area = AreaEnum::AREA_FRONTEND): void
    {
        $this->requestMock->expects($area === AreaEnum::AREA_FRONTEND ? self::once() : $this->never())
            ->method('isHomepage')
            ->willReturn($isHomepage);
        $this->requestMock->expects(self::once())
            ->method('getArea')
            ->willReturn($area);
    }

    private function getTemplateContent(): string
    {
        return <<<HTML
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

    public function testProcessIsHomepage(): void
    {
        $this->setUpPageCssClassesMockExpectations(0);
        $this->setUpRequestMockExpectations(true);

        $expected = <<<HTML
<html>
<head>
<title>Foobar</title>
</head>
<body class="foo foo-bar-baz is-homepage">
<p>Baz</p>
</body>
</html>
HTML;

        self::assertEquals(
            $expected,
            $this->plugin->__invoke($this->getTemplateContent(), $this->smartyInternalTemplateMock)
        );
    }

    public function testProcessInAdmin(): void
    {
        $this->setUpPageCssClassesMockExpectations(0);
        $this->setUpRequestMockExpectations(false, AreaEnum::AREA_ADMIN);

        $expected = <<<HTML
<html>
<head>
<title>Foobar</title>
</head>
<body class="foo foo-bar-baz in-admin">
<p>Baz</p>
</body>
</html>
HTML;

        self::assertEquals(
            $expected,
            $this->plugin->__invoke($this->getTemplateContent(), $this->smartyInternalTemplateMock)
        );
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
}
