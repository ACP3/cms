<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Helper\Enum\IndexPaginatedContentEnum;
use ACP3\Modules\ACP3\Seo\Installer\Schema as SeoSchema;

class AdminSettingsViewProvider
{
    public function __construct(private Forms $formsHelper, private FormToken $formTokenHelper, private RequestInterface $request, private SettingsInterface $settings, private Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $seoSettings = $this->settings->getSettings(SeoSchema::MODULE_NAME);

        $robots = [
            1 => $this->translator->t('seo', 'robots_index_follow'),
            2 => $this->translator->t('seo', 'robots_index_nofollow'),
            3 => $this->translator->t('seo', 'robots_noindex_follow'),
            4 => $this->translator->t('seo', 'robots_noindex_nofollow'),
        ];

        $indexPaginatedContent = [
            IndexPaginatedContentEnum::INDEX_FIST_PAGE_ONLY->value => $this->translator->t('seo', 'index_first_page_only'),
            IndexPaginatedContentEnum::INDEX_ALL_PAGES->value => $this->translator->t('seo', 'index_all_pages'),
        ];

        $sitemapSaveMode = [
            1 => $this->translator->t('seo', 'sitemap_save_mode_automatically'),
            2 => $this->translator->t('seo', 'sitemap_save_mode_manually'),
        ];

        return [
            'robots' => $this->formsHelper->choicesGenerator('robots', $robots, $seoSettings['robots']),
            'index_paginated_content' => $this->formsHelper->checkboxGenerator(
                'index_paginated_content',
                $indexPaginatedContent,
                $seoSettings['index_paginated_content']
            ),
            'sitemap_is_enabled' => $this->formsHelper->yesNoCheckboxGenerator(
                'sitemap_is_enabled',
                $seoSettings['sitemap_is_enabled']
            ),
            'sitemap_separate' => $this->formsHelper->yesNoCheckboxGenerator(
                'sitemap_separate',
                $seoSettings['sitemap_separate']
            ),
            'sitemap_save_mode' => $this->formsHelper->checkboxGenerator(
                'sitemap_save_mode',
                $sitemapSaveMode,
                $seoSettings['sitemap_save_mode']
            ),
            'form' => array_merge($seoSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
