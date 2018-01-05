<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\View\Block\Frontend;

use ACP3\Core\Date;
use ACP3\Core\Test\View\Block\AbstractListingBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;
use ACP3\Modules\ACP3\Articles\View\Block\Frontend\ArticlesListingBlock;

class ArticlesListingBlockTest extends AbstractListingBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $date = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();

        $articleRepository = $this->getMockBuilder(ArticlesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $articleRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(30);

        $articleRepository->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        return new ArticlesListingBlock($this->context, $date, $articleRepository);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'articles',
            'pagination',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedModuleName(): string
    {
        return 'articles';
    }
}
