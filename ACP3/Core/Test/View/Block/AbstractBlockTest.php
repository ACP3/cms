<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Block;


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
        $this->context = $this->getMockBuilder(BlockContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['getView', 'getBreadcrumb', 'getTitle', 'getTranslator'])
            ->getMock();
    }

    /**
     * @return BlockInterface
     */
    abstract protected function instantiateBlock(): BlockInterface;

    public function testRenderReturnsArray()
    {
        $result = $this->block->render();

        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $result = $this->block->render();

        $this->assertEquals($this->getExpectedArrayKeys(), array_keys($result));
    }

    /**
     * Returns the expected associative array which the $block->render() method returns
     *
     * @return array
     */
    abstract protected function getExpectedArrayKeys(): array;
}
