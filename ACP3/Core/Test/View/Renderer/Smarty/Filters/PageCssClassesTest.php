<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\View\Renderer\Smarty\Filters;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\Request;
use ACP3\Core\Test\View\Renderer\Smarty\AbstractPluginTest;
use ACP3\Core\View\Renderer\Smarty\Filters\PageCssClasses;

class PageCssClassesTest extends AbstractPluginTest
{
    /**
     * @var PageCssClasses
     */
    protected $plugin;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $pageCssClassesMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $smartyInternalTemplateMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->plugin = new PageCssClasses(
            $this->pageCssClassesMock,
            $this->requestMock
        );
    }

    private function setUpMockObjects()
    {
        $this->pageCssClassesMock = $this->createMock(\ACP3\Core\Assets\PageCssClasses::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->smartyInternalTemplateMock = $this->createMock(\Smarty_Internal_Template::class);
    }

    /**
     * @return string
     */
    protected function getExpectedExtensionName()
    {
        return 'output';
    }

    public function testProcessInFrontend()
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

        $this->assertEquals(
            $expected,
            $this->plugin->__invoke($this->getTemplateContent(), $this->smartyInternalTemplateMock)
        );
    }

    /**
     * @param int $getDetailsCalls
     */
    private function setUpPageCssClassesMockExpectations($getDetailsCalls = 1)
    {
        $this->pageCssClassesMock
            ->expects($this->once())
            ->method('getModule')
            ->willReturn('foo');
        $this->pageCssClassesMock
            ->expects($this->once())
            ->method('getControllerAction')
            ->willReturn('foo-bar-baz');
        $this->pageCssClassesMock
            ->expects($this->exactly($getDetailsCalls))
            ->method('getDetails')
            ->willReturn('foo-bar-pagetitle');
    }

    /**
     * @param bool   $isHomepage
     * @param string $area
     */
    private function setUpRequestMockExpectations($isHomepage = false, $area = AreaEnum::AREA_FRONTEND)
    {
        $this->requestMock->expects($area === AreaEnum::AREA_FRONTEND ? $this->once() : $this->never())
            ->method('isHomepage')
            ->willReturn($isHomepage);
        $this->requestMock->expects($this->once())
            ->method('getArea')
            ->willReturn($area);
    }

    /**
     * @return string
     */
    private function getTemplateContent()
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

    public function testProcessIsHomepage()
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

        $this->assertEquals(
            $expected,
            $this->plugin->__invoke($this->getTemplateContent(), $this->smartyInternalTemplateMock)
        );
    }

    public function testProcessInAdmin()
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

        $this->assertEquals(
            $expected,
            $this->plugin->__invoke($this->getTemplateContent(), $this->smartyInternalTemplateMock)
        );
    }

    public function testProcessWithNoHtmlBodyTag()
    {
        $templateContent = <<<HTML
<p>Baz</p>
HTML;

        $this->assertEquals(
            $templateContent,
            $this->plugin->__invoke($templateContent, $this->smartyInternalTemplateMock)
        );
    }
}
