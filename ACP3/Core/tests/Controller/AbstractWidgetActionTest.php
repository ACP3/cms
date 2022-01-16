<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\View;
use PHPUnit\Framework\TestCase;

class AbstractWidgetActionTest extends TestCase
{
    /**
     * @var AbstractWidgetActionImpl
     */
    private $displayAction;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|WidgetContext
     */
    private $widgetContextMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|View
     */
    private $viewMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();
        $this->displayAction = new AbstractWidgetActionImpl(
            $this->widgetContextMock
        );
    }

    private function setUpMockObjects(): void
    {
        $this->viewMock = $this->createMock(View::class);

        $this->widgetContextMock = $this->createMock(WidgetContext::class);
        $this->widgetContextMock->method('getView')->willReturn($this->viewMock);
    }

    public function testDisplayWithStringActionResult(): void
    {
        $actionResult = 'foo-bar-baz';

        $actual = $this->displayAction->display($actionResult);

        self::assertEquals($actionResult, $actual->getContent());
    }

    public function testDisplayWithVoidActionResult(): void
    {
        $templateOutput = 'foo-bar-baz';
        $this->setUpViewMockExpectations($templateOutput);
        $actionResult = null;

        $actual = $this->displayAction->display($actionResult);

        self::assertEquals($templateOutput, $actual->getContent());
    }

    private function setUpViewMockExpectations(string $templateOutput, array $tplVars = []): void
    {
        $this->viewMock->expects(self::once())
            ->method('fetchTemplate')
            ->with('Foo/Frontend/index.index.tpl')
            ->willReturn($templateOutput);
        if (!empty($tplVars)) {
            $this->viewMock->expects(self::once())
                ->method('assign')
                ->with($tplVars)
                ->willReturnSelf();
        }
    }

    public function testDisplayWithArrayActionResult(): void
    {
        $templateOutput = 'foo-bar-baz-array';
        $actionResult = [
            'lorem-ispum' => 'lorem ipsum dolor',
        ];

        $this->setUpViewMockExpectations($templateOutput, $actionResult);

        $actual = $this->displayAction->display($actionResult);

        self::assertEquals($templateOutput, $actual->getContent());
    }
}
