<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GalleryManageFormBlock;

class GalleryManageFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var GalleryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->repository = $this->getMockBuilder(GalleryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new GalleryManageFormBlock($this->context, $this->repository);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'gallery_id',
            'form',
            'form_token',
            'SEO_URI_PATTERN',
            'SEO_ROUTE_NAME',
        ];
    }
}
