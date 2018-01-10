<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     * @var Date|\PHPUnit_Framework_MockObject_MockObject
     */
    private $date;
    /**
     * @var ArticlesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $articleRepository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->date = $this->createMock(Date::class);

        $this->date
            ->expects($this->exactly(2))
            ->method('getCurrentDateTime')
            ->willReturn('2017-12-01 10:02:43');

        $this->articleRepository = $this->createMock(ArticlesRepository::class);

        $this->articleRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(30);

        $this->articleRepository->expects($this->once())
            ->method('getAll')
            ->willReturn([]);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new ArticlesListingBlock($this->context, $this->date, $this->articleRepository);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'articles',
            'pagination',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedModuleName(): string
    {
        return 'articles';
    }
}
