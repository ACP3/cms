<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\View\Block;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Core\View\Block\Context\BlockContext;

abstract class AbstractBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockInterface
     */
    protected $block;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->block = $this->instantiateBlock();
    }

    protected function setUpMockObjects()
    {
        $this->context = $this->getMockBuilder($this->getContextMockFQCN())
            ->disableOriginalConstructor()
            ->setMethods($this->getContextMockMethods())
            ->getMock();

        $breadcrumb = $this->getMockBuilder(Steps::class)
            ->disableOriginalConstructor()
            ->getMock();

        $breadcrumb->expects($this->any())
            ->method('append')
            ->willReturnSelf();

        $this->context->expects($this->once())
            ->method('getBreadcrumb')
            ->willReturn($breadcrumb);

        $translator = $this->getMockBuilder(TranslatorInterface::class)
            ->setMethods(['t'])
            ->getMock();

        $translator->expects($this->any())
            ->method('t')
            ->willReturn('foo-bar');

        $this->context->expects($this->atLeastOnce())
            ->method('getTranslator')
            ->willReturn($translator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContextMockFQCN(): string
    {
        return BlockContext::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContextMockMethods(): array
    {
        return ['getView', 'getBreadcrumb', 'getTitle', 'getTranslator'];
    }

    /**
     * @return BlockInterface
     */
    abstract protected function instantiateBlock(): BlockInterface;

    public function testRenderReturnsArray()
    {
        $result = $this->block->render();

        $this->assertTrue(\is_array($result));
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $result = $this->block->render();

        $this->assertEquals($this->getExpectedArrayKeys(), \array_keys($result));
    }

    /**
     * Returns the expected associative array which the $block->render() method returns.
     *
     * @return array
     */
    abstract protected function getExpectedArrayKeys(): array;
}
