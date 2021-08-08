<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\News\Installer\Schema as NewsSchema;
use ACP3\Modules\ACP3\News\Repository\NewsRepository;

class NewsDetailsViewProvider
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Repository\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(
        NewsRepository $newsRepository,
        CategoryRepository $categoryRepository,
        RequestInterface $request,
        SettingsInterface $settings,
        Steps $breadcrumb,
        Title $title,
        Translator $translator,
        View $view
    ) {
        $this->newsRepository = $newsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->request = $request;
        $this->settings = $settings;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->translator = $translator;
        $this->view = $view;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $newsId): array
    {
        $newsSettings = $this->settings->getSettings(NewsSchema::MODULE_NAME);

        $news = $this->newsRepository->getOneById($newsId);

        $this->addBreadcrumbSteps(
            $news,
            $news['category_id'],
            $newsSettings['category_in_breadcrumb'] == 1
        );

        $news['text'] = $this->view->fetchStringAsTemplate($news['text']);
        $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

        return [
            'news' => $news,
            'dateformat' => $newsSettings['dateformat'],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function addBreadcrumbSteps(array $news, int $categoryId, bool $showCategoriesInBreadcrumb): void
    {
        $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
        if ($showCategoriesInBreadcrumb === true) {
            foreach ($this->categoryRepository->fetchNodeWithParents($categoryId) as $category) {
                $this->breadcrumb->append($category['title'], 'news/index/index/cat_' . $category['id']);
            }
        }
        $this->breadcrumb->append(
            $news['title'],
            $this->request->getQuery()
        );
        $this->title->setPageTitle($news['title']);
    }
}
