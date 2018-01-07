<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;

class NewsletterDetailsBlock extends AbstractBlock
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $newsletter = $this->getData();

        $this->breadcrumb
            ->append($this->translator->t('newsletter', 'index'), 'newsletter')
            ->append($this->translator->t('newsletter', 'frontend_archive_index'), 'newsletter/archive')
            ->append($newsletter['title']);
        $this->title->setPageTitle($newsletter['title']);

        return [
            'newsletter' => $newsletter,
        ];
    }
}
