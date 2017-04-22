<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Articles\View\Block\Admin\ArticleFormBlock;

class ArticleFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new ArticleFormBlock($this->context);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'active',
            'form',
            'form_token',
            'SEO_URI_PATTERN',
            'SEO_ROUTE_NAME'
        ];
    }
}
