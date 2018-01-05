<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\View\Block\Frontend\FileDetailsBlock;

class FileDetailsBlockTest extends AbstractBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'dateformat' => 'long',
                'comments' => 1,
            ]);

        $categoriesRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoriesRepository->expects($this->once())
            ->method('fetchNodeWithParents')
            ->with(2)
            ->willReturn([]);

        return new FileDetailsBlock($this->context, $settings, $categoriesRepository);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData([
            'category_id' => 2,
            'category_title' => 'Category Foo',
            'title' => 'Foo',
            'text' => 'Foo Bar Baz',
            'comments' => 1,
        ]);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData([
            'category_id' => 2,
            'category_title' => 'Category Foo',
            'title' => 'Foo',
            'text' => 'Foo Bar Baz',
            'comments' => 1,
        ]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'file',
            'dateformat',
            'comments_allowed',
        ];
    }
}
