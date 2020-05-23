<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    private $stringFormatter;
    /**
     * @var Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    private $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    private $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \ACP3\Core\SEO\MetaStatementsServiceInterface
     */
    private $metaStatements;
    /**
     * @var array
     */
    private $newsSettings = [];
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        ResultsPerPage $resultsPerPage,
        Core\Router\RouterInterface $router,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Pagination $pagination,
        News\Model\Repository\NewsRepository $newsRepository,
        Categories\Helpers $categoriesHelpers,
        Categories\Model\Repository\CategoryRepository $categoryRepository,
        MetaStatementsServiceInterface $metaStatements
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->stringFormatter = $stringFormatter;
        $this->newsRepository = $newsRepository;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->categoryRepository = $categoryRepository;
        $this->metaStatements = $metaStatements;
        $this->router = $router;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $cat = 0): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $this->newsSettings = $this->config->getSettings(News\Installer\Schema::MODULE_NAME);

        $this->addBreadcrumbStep($cat);

        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($this->resultsPerPage->getResultsPerPage(News\Installer\Schema::MODULE_NAME))
            ->setTotalResults($this->fetchNewsCount($cat, $time));

        $newsList = $this->fetchNews($cat, $time);

        foreach ($newsList as $i => $news) {
            $newsList[$i]['text'] = $this->view->fetchStringAsTemplate($news['text']);
            if ($this->newsSettings['readmore'] == 1 && $news['readmore'] == 1) {
                $newsList[$i]['text'] = $this->addReadMoreLink($news);
            }
        }

        try {
            return [
                'news' => $newsList,
                'dateformat' => $this->newsSettings['dateformat'],
                'categories' => $this->categoriesHelpers->categoriesList('news', $cat),
                'pagination' => $this->pagination->render(),
            ];
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
     */
    private function fetchNews(int $categoryId, string $time): array
    {
        if (!empty($categoryId)) {
            $news = $this->newsRepository->getAllByCategoryId(
                $this->categoryRepository->getAllSiblingsAsId($categoryId),
                $time,
                $this->pagination->getResultsStartOffset(),
                $this->resultsPerPage->getResultsPerPage(News\Installer\Schema::MODULE_NAME)
            );
        } else {
            $news = $this->newsRepository->getAll(
                $time,
                $this->pagination->getResultsStartOffset(),
                $this->resultsPerPage->getResultsPerPage(News\Installer\Schema::MODULE_NAME)
            );
        }

        return $news;
    }

    protected function addReadMoreLink(array $news): string
    {
        $readMoreLink = '...<a href="' . $this->router->route('news/details/id_' . $news['id']) . '">[';
        $readMoreLink .= $this->translator->t('news', 'readmore') . "]</a>\n";

        return $this->stringFormatter->shortenEntry(
            $news['text'],
            $this->newsSettings['readmore_chars'],
            50,
            $readMoreLink
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function addBreadcrumbStep(int $categoryId): void
    {
        if ($categoryId !== 0 && $this->newsSettings['category_in_breadcrumb'] == 1) {
            $this->metaStatements->setCanonicalUri($this->router->route('news', true));

            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
            foreach ($this->categoryRepository->fetchNodeWithParents($categoryId) as $category) {
                $this->breadcrumb->append($category['title'], 'news/index/index/cat_' . $category['id']);
            }
        }
    }
}
