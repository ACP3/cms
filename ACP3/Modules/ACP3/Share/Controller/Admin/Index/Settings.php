<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;
use ACP3\Modules\ACP3\Seo\Helper\Enum\IndexPaginatedContentEnum;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Core\Helpers\Forms                                      $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                  $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $seoSettings = $this->config->getSettings(Seo\Installer\Schema::MODULE_NAME);

        $robots = [
            1 => $this->translator->t('seo', 'robots_index_follow'),
            2 => $this->translator->t('seo', 'robots_index_nofollow'),
            3 => $this->translator->t('seo', 'robots_noindex_follow'),
            4 => $this->translator->t('seo', 'robots_noindex_nofollow'),
        ];

        $indexPaginatedContent = [
            IndexPaginatedContentEnum::INDEX_FIST_PAGE_ONLY => $this->translator->t('seo', 'index_first_page_only'),
            IndexPaginatedContentEnum::INDEX_ALL_PAGES => $this->translator->t('seo', 'index_all_pages'),
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
            'form' => \array_merge($seoSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'meta_description' => $this->get('core.helpers.secure')->strEncode($formData['meta_description']),
                'meta_keywords' => $this->get('core.helpers.secure')->strEncode($formData['meta_keywords']),
                'robots' => (int) $formData['robots'],
                'sitemap_is_enabled' => (int) $formData['sitemap_is_enabled'],
                'sitemap_save_mode' => (int) $formData['sitemap_save_mode'],
                'sitemap_separate' => (int) $formData['sitemap_separate'],
            ];

            return $this->config->saveSettings($data, Seo\Installer\Schema::MODULE_NAME);
        });
    }
}
