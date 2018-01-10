<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Frontend;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractListingBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use ACP3\Modules\ACP3\Files\View\Block\Frontend\FilesListingBlock;

class FilesListingBlockTest extends AbstractListingBlockTest
{
    /**
     * @var Date|\PHPUnit_Framework_MockObject_MockObject
     */
    private $date;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var FilesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesRepository;
    /**
     * @var CategoriesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoriesRepository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->date = $this->createMock(Date::class);

        $this->date
            ->expects($this->exactly(2))
            ->method('getCurrentDateTime')
            ->willReturn('2017-12-01 10:02:43');

        $this->settings = $this->createMock(SettingsInterface::class);

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'dateformat' => 'long',
            ]);

        $this->filesRepository = $this->createMock(FilesRepository::class);

        $this->filesRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(30);

        $this->filesRepository->expects($this->once())
            ->method('getAllByCategoryId')
            ->willReturn([]);

        $this->categoriesRepository = $this->createMock(CategoriesRepository::class);

        $this->categoriesRepository->expects($this->once())
            ->method('fetchNodeWithParents')
            ->with(2)
            ->willReturn([]);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new FilesListingBlock(
            $this->context,
            $this->date,
            $this->settings,
            $this->filesRepository,
            $this->categoriesRepository
        );
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['category_id' => 2]);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['category_id' => 2]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'categories',
            'dateformat',
            'files',
            'pagination',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedModuleName(): string
    {
        return 'files';
    }
}
