<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\View\Block\Admin;


use ACP3\Core\View\Block\AbstractBlock;

class SystemCacheBlock extends AbstractBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'cache_types' => [
                'general',
                'images',
                'minify',
                'page',
                'templates'
            ]
        ];
    }
}
