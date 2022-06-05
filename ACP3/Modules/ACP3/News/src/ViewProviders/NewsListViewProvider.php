<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Date;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Pagination;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Categories\Helpers as CategoriesHelper;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\News\Installer\Schema as NewsSchema;
use ACP3\Modules\ACP3\News\Repository\NewsRepository;

class NewsListViewProvider
{
    public function __construct(private readonly CategoriesHelper $categoriesHelpers, private readonly CategoryRepository $categoryRepository, private readonly Date $date, private readonly MetaStatementsServiceInterface $metaStatements, private readonly NewsRepository $newsRepository, private readonly Pagination $pagination, private readonly ResultsPerPage $resultsPerPage, private readonly RouterInterface $router, private readonly SettingsInterface $settings, private readonly StringFormatter $stringFormatter, private readonly Steps $breadcrumb, private readonly Translator $translator, private readonly View $view)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?int $categoryId): array
    {
        $newsSettings = $this->settings->getSettings(NewsSchema::MODULE_NAME);

        $this->addBreadcrumbStep($categoryId, $newsSettings['category_in_breadcrumb'] == 1);

        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($this->resultsPerPage->getResultsPerPage(NewsSchema::MODULE_NAME))
            ->setTotalResults($this->fetchNewsCount($categoryId, $time));

        $newsList = $this->fetchNews($categoryId, $time);

        foreach ($newsList as $i => $news) {
            $newsList[$i]['text'] = $this->view->fetchStringAsTemplate($news['text']);
            if ($newsSettings['readmore'] == 1 && $news['readmore'] == 1) {
                $newsList[$i]['text'] = $this->addReadMoreLink($news, $newsSettings['readmore_chars']);
            }
        }

        return [
            'news' => $newsList,
            'dateformat' => $newsSettings['dateformat'],
            'categories' => $this->categoriesHelpers->categoriesList('news', $categoryId),
            'pagination' => $this->pagination->render(),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchNewsCount(int $categoryId, string $time): int
    {
        if (!empty($categoryId)) {
            return $this->newsRepository->countAllByCategoryId(
                $this->categoryRepository->getAllSiblingsAsId($categoryId),
                $time
            );
        }

        return $this->newsRepository->countAll($time);
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchNews(int $categoryId, string $time): array
    {
        if (!empty($categoryId)) {
            $news = $this->newsRepository->getAllByCategoryId(
                $this->categoryRepository->getAllSiblingsAsId($categoryId),
                $time,
                $this->pagination->getResultsStartOffset(),
                $this->resultsPerPage->getResultsPerPage(NewsSchema::MODULE_NAME)
            );
        } else {
            $news = $this->newsRepository->getAll(
                $time,
                $this->pagination->getResultsStartOffset(),
                $this->resultsPerPage->getResultsPerPage(NewsSchema::MODULE_NAME)
            );
        }

        return $news;
    }

    /**
     * @param array<string, mixed> $news
     */
    private function addReadMoreLink(array $news, int $readMoreCharacters): string
    {
        $readMoreLink = '...<a href="' . $this->router->route('news/details/id_' . $news['id']) . '">[';
        $readMoreLink .= $this->translator->t('news', 'readmore') . "]</a>\n";

        return $this->stringFormatter->shortenEntry(
            $news['text'],
            $readMoreCharacters,
            50,
            $readMoreLink
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function addBreadcrumbStep(int $categoryId, bool $showCategoryInBreadcrumb): void
    {
        if ($categoryId !== 0 && $showCategoryInBreadcrumb === true) {
            $this->metaStatements->setCanonicalUri($this->router->route('news', true));

            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
            foreach ($this->categoryRepository->fetchNodeWithParents($categoryId) as $category) {
                $this->breadcrumb->append($category['title'], 'news/index/index/cat_' . $category['id']);
            }
        }
    }
}
