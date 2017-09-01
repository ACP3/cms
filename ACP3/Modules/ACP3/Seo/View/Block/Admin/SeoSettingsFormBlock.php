<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\View\Block\Admin;

use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Modules\ACP3\Seo\Helper\Enum\IndexPaginatedContentEnum;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class SeoSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $seoSettings = $this->getData();

        $robots = [
            1 => $this->translator->t('seo', 'robots_index_follow'),
            2 => $this->translator->t('seo', 'robots_index_nofollow'),
            3 => $this->translator->t('seo', 'robots_noindex_follow'),
            4 => $this->translator->t('seo', 'robots_noindex_nofollow')
        ];

        $indexPaginatedContent = [
            IndexPaginatedContentEnum::INDEX_FIST_PAGE_ONLY => $this->translator->t('seo', 'index_first_page_only'),
            IndexPaginatedContentEnum::INDEX_ALL_PAGES => $this->translator->t('seo', 'index_all_pages')
        ];

        $sitemapSaveMode = [
            1 => $this->translator->t('seo', 'sitemap_save_mode_automatically'),
            2 => $this->translator->t('seo', 'sitemap_save_mode_manually'),
        ];

        return [
            'robots' => $this->forms->choicesGenerator('robots', $robots, $seoSettings['robots']),
            'index_paginated_content' => $this->forms->checkboxGenerator(
                'index_paginated_content',
                $indexPaginatedContent,
                $seoSettings['index_paginated_content']
            ),
            'sitemap_is_enabled' => $this->forms->yesNoCheckboxGenerator(
                'sitemap_is_enabled',
                $seoSettings['sitemap_is_enabled']
            ),
            'sitemap_separate' => $this->forms->yesNoCheckboxGenerator(
                'sitemap_separate',
                $seoSettings['sitemap_separate']
            ),
            'sitemap_save_mode' => $this->forms->checkboxGenerator(
                'sitemap_save_mode',
                $sitemapSaveMode,
                $seoSettings['sitemap_save_mode']
            ),
            'form' => array_merge($seoSettings, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
