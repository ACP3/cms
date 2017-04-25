<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Block;


use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Core\View\Block\FormBlockInterface;

abstract class AbstractFormBlockTest extends AbstractBlockTest
{
    /**
     * @var FormBlockInterface
     */
    protected $block;

    protected function setUpMockObjects()
    {
        $this->context = $this->getMockBuilder(FormBlockContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['getView', 'getBreadcrumb', 'getTitle', 'getTranslator', 'getForms', 'getFormToken'])
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
    }
}