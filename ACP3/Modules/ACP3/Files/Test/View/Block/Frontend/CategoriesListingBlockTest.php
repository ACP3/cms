<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Frontend;

use ACP3\Core\Test\View\Block\AbstractBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\View\Block\Frontend\CategoriesListingBlock;

class CategoriesListingBlockTest extends AbstractBlockTest
{
    /**
     * @var CategoriesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoriesRepository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->categoriesRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CategoriesListingBlock($this->context, $this->categoriesRepository);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'categories',
        ];
    }
}
