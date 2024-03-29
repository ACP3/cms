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
    public function __construct(private readonly NewsRepository $newsRepository, private readonly CategoryRepository $categoryRepository, private readonly RequestInterface $request, private readonly SettingsInterface $settings, private readonly Steps $breadcrumb, private readonly Title $title, private readonly Translator $translator, private readonly View $view)
    {
    }

    /**
     * @return array<string, mixed>
     *
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
     * @param array<string, mixed> $news
     *
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
