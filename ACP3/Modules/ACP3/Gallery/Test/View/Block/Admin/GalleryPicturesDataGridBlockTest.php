<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Test\View\Block\AbstractDataGridBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GalleryPicturesDataGridBlock;

class GalleryPicturesDataGridBlockTest extends AbstractDataGridBlockTest
{
    /**
     * @var ACLInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $acl;
    /**
     * @var GalleryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $galleryRepository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->acl = $this->getMockBuilder(ACLInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->galleryRepository = $this->getMockBuilder(GalleryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new GalleryPicturesDataGridBlock($this->context, $this->acl, $this->galleryRepository);
    }

    public function testRenderReturnsArray()
    {
        $this->setUpGalleryRepositoryExpectations();

        $this->block->setData(['results' => [], 'gallery_id' => 2]);

        parent::testRenderReturnsArray();
    }

    private function setUpGalleryRepositoryExpectations()
    {
        $this->galleryRepository->expects($this->once())
            ->method('getOneById')
            ->with(2)
            ->willReturn([
                'title' => 'Test-Gallery'
            ]);
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->setUpGalleryRepositoryExpectations();

        $this->block->setData(['results' => [], 'gallery_id' => 2]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'gallery_id',
            'grid',
            'show_mass_delete_button'
        ];
    }
}
