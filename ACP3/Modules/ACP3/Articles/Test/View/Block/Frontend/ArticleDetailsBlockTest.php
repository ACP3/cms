<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\View\Block\Frontend;

use ACP3\Core\Helpers\PageBreaks;
use ACP3\Core\Http\Request;
use ACP3\Core\Test\View\Block\AbstractBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Articles\View\Block\Frontend\ArticleDetailsBlock;

class ArticleDetailsBlockTest extends AbstractBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pageBreaksHelper = $this->getMockBuilder(PageBreaks::class)
            ->disableOriginalConstructor()
            ->setMethods(['splitTextIntoPages'])
            ->getMock();

        $pageBreaksHelper->expects($this->once())
            ->method('splitTextIntoPages')
            ->willReturn([]);

        return new ArticleDetailsBlock($this->context, $request, $pageBreaksHelper);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['title' => 'foo', 'text' => 'bar']);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['title' => 'foo', 'text' => 'bar']);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'page'
        ];
    }
}
