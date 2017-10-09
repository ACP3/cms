<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $date = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();

        $settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'dateformat' => 'long'
            ]);

        $filesRepository = $this->getMockBuilder(FilesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filesRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(30);

        $filesRepository->expects($this->once())
            ->method('getAllByCategoryId')
            ->willReturn([]);

        $categoryRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryRepository->expects($this->once())
            ->method('fetchNodeWithParents')
            ->with(2)
            ->willReturn([]);

        return new FilesListingBlock($this->context, $date, $settings, $filesRepository, $categoryRepository);
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
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'categories',
            'dateformat',
            'files',
            'pagination'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedModuleName(): string
    {
        return 'files';
    }
}
