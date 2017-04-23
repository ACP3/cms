<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GalleryFormBlock;

class GalleryFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new GalleryFormBlock($this->context);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'gallery_id',
            'form',
            'form_token',
            'SEO_URI_PATTERN',
            'SEO_ROUTE_NAME'
        ];
    }
}
