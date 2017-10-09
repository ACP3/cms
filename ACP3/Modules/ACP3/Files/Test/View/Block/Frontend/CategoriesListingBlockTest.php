<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Frontend;

use ACP3\Core\Test\View\Block\AbstractBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\View\Block\Frontend\CategoriesListingBlock;

class CategoriesListingBlockTest extends AbstractBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $categoriesRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new CategoriesListingBlock($this->context, $categoriesRepository);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'categories'
        ];
    }
}
