<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GalleryPictureManageFormBlock;

class GalleryPictureManageFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var GalleryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $galleryRepositoryMock;
    /**
     * @var GalleryPicturesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $galleryPicturesRepositoryMock;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsMock;
    /**
     * @var Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modulesMock;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->galleryPicturesRepositoryMock = $this->createMock(GalleryPicturesRepository::class);
        $this->settingsMock = $this->createMock(SettingsInterface::class);
        $this->modulesMock = $this->createMock(Modules::class);
        $this->galleryRepositoryMock = $this->createMock(GalleryRepository::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new GalleryPictureManageFormBlock(
            $this->context,
            $this->galleryPicturesRepositoryMock,
            $this->settingsMock,
            $this->modulesMock,
            $this->galleryRepositoryMock
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'gallery_id',
            'form_token',
            'options',
        ];
    }

    public function testRenderReturnsArray()
    {
        $this->setUpGalleryRepositoryMockExpectations();
        $this->setUpSettingsMockExpectations();

        $this->block->setData(['gallery_id' => 2]);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->setUpGalleryRepositoryMockExpectations();
        $this->setUpSettingsMockExpectations();

        $this->block->setData(['gallery_id' => 2]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    private function setUpGalleryRepositoryMockExpectations(): void
    {
        $this->galleryRepositoryMock
            ->expects($this->once())
            ->method('getGalleryTitle')
            ->with(2)
            ->willReturn('Foo');
    }

    private function setUpSettingsMockExpectations(): void
    {
        $this->settingsMock
            ->expects($this->once())
            ->method('getSettings')
            ->with('gallery')
            ->willReturn([
                'overlay' => 1,
                'comments' => 1,
            ]);
    }
}
