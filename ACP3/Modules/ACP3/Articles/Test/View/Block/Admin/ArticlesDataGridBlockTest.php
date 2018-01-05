<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractDataGridBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Articles\View\Block\Admin\ArticlesDataGridBlock;

class ArticlesDataGridBlockTest extends AbstractDataGridBlockTest
{
    /**
     * @return BlockInterface
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new ArticlesDataGridBlock($this->context);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'grid',
            'show_mass_delete_button',
        ];
    }
}
