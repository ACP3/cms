<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Categories\Helpers as CategoriesHelpers;
use ACP3\Modules\ACP3\News\Helpers as NewsHelpers;
use ACP3\Modules\ACP3\News\Installer\Schema as NewsSchema;

class AdminNewsEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    private $categoriesHelpers;

    public function __construct(
        CategoriesHelpers $categoriesHelpers,
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        SettingsInterface $settings,
        Title $title,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->settings = $settings;
        $this->title = $title;
        $this->translator = $translator;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $news): array
    {
        $this->title->setPageTitlePrefix($news['title']);

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator('active', $news['active']),
            'categories' => $this->categoriesHelpers->categoriesList(
                NewsSchema::MODULE_NAME,
                $news['category_id'],
                true
            ),
            'options' => $this->fetchOptions($news['readmore']),
            'target' => $this->formsHelper->linkTargetChoicesGenerator('target', $news['target']),
            'form' => array_merge($news, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'SEO_URI_PATTERN' => NewsHelpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => !empty($news['id']) ? sprintf(NewsHelpers::URL_KEY_PATTERN, $news['id']) : '',
        ];
    }

    private function fetchOptions(int $readMoreValue): array
    {
        $settings = $this->settings->getSettings(NewsSchema::MODULE_NAME);
        $options = [];
        if ($settings['readmore'] == 1) {
            $readMore = [
                '1' => $this->translator->t('news', 'activate_readmore'),
            ];

            $options = $this->formsHelper->checkboxGenerator('readmore', $readMore, $readMoreValue);
        }

        return $options;
    }
}
