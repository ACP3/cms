<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Test\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractListingBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Comments\Model\Repository\CommentsRepository;
use ACP3\Modules\ACP3\Comments\View\Block\Frontend\CommentsListingBlock;

class CommentsListingBlockTest extends AbstractListingBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settings->expects($this->exactly(2))
            ->method('getSettings')
            ->willReturn(['dateformat' => 'long', 'emoticons' => 1]);

        $commentRepository = $this->getMockBuilder(CommentsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commentRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(30);

        $commentRepository->expects($this->once())
            ->method('getAllByModule')
            ->willReturn([]);

        return new CommentsListingBlock($this->context, $settings, $commentRepository);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['resultId' => 30, 'moduleId' => 1]);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['resultId' => 30, 'moduleId' => 1]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'comments',
            'dateformat',
            'pagination',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedModuleName(): string
    {
        return 'comments';
    }
}
