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
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var CategoriesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoriesRepository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'dateformat' => 'long',
                'comments' => 1,
            ]);

        $this->categoriesRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoriesRepository->expects($this->once())
            ->method('fetchNodeWithParents')
            ->with(2)
            ->willReturn([]);
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new FileDetailsBlock($this->context, $this->settings, $this->categoriesRepository);
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
