<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;

class NotFoundBlock extends AbstractBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->breadcrumb->append($this->translator->t('errors', 'frontend_index_not_found'));

        return [];
    }
}
