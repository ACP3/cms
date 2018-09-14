<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments\Helpers;
use ACP3\Modules\ACP3\News;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends AbstractAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    private $commentsHelpers;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                     $context
     * @param \ACP3\Core\Date                                                   $date
     * @param \ACP3\Core\Helpers\StringFormatter                                $stringFormatter
     * @param \ACP3\Core\Pagination                                             $pagination
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository           $newsRepository
     * @param \ACP3\Modules\ACP3\Categories\Helpers                             $categoriesHelpers
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository $categoryRepository
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements|null                 $metaStatements
     * @param \ACP3\Modules\ACP3\Comments\Helpers                               $commentsHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Pagination $pagination,
        News\Model\Repository\NewsRepository $newsRepository,
        Categories\Helpers $categoriesHelpers,
        Categories\Model\Repository\CategoryRepository $categoryRepository,
        ?MetaStatements $metaStatements = null,
        ?Helpers $commentsHelpers = null
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->stringFormatter = $stringFormatter;
        $this->newsRepository = $newsRepository;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->categoryRepository = $categoryRepository;
        $this->metaStatements = $metaStatements;
        $this->commentsHelpers = $commentsHelpers;
    }

    /**
     * @param int $cat
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $cat = 0)
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $this->addBreadcrumbStep($cat);

        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($this->resultsPerPage->getResultsPerPage(News\Installer\Schema::MODULE_NAME))
            ->setTotalResults($this->newsRepository->countAll($time, $cat));

        $news = $this->fetchNews($cat, $time);
        $cNews = \count($news);

        for ($i = 0; $i < $cNews; ++$i) {
            $news[$i]['text'] = $this->view->fetchStringAsTemplate($news[$i]['text']);
            if ($this->commentsActive === true && $news[$i]['comments'] == 1) {
                $news[$i]['comments_count'] = $this->commentsHelpers->commentsCount(
                    News\Installer\Schema::MODULE_NAME,
                    $news[$i]['id']
                );
            }
            if ($this->newsSettings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                $news[$i]['text'] = $this->addReadMoreLink($news[$i]);
            }
        }

        return [
            'news' => $news,
            'dateformat' => $this->newsSettings['dateformat'],
            'categories' => $this->categoriesHelpers->categoriesList('news', $cat),
            'pagination' => $this->pagination->render(),
        ];
    }

    /**
     * @param int    $categoryId
     * @param string $time
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function fetchNews(int $categoryId, string $time)
    {
        if (!empty($categoryId)) {
            $news = $this->newsRepository->getAllByCategoryId(
                $categoryId,
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

    /**
     * @param array $news
     *
     * @return string
     */
    protected function addReadMoreLink(array $news)
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
     * @param int $cat
     */
    protected function addBreadcrumbStep(int $cat)
    {
        if ($cat !== 0 && $this->newsSettings['category_in_breadcrumb'] == 1) {
            if ($this->metaStatements !== null) {
                $this->metaStatements->setCanonicalUri($this->router->route('news', true));
            }

            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
            $category = $this->categoryRepository->getTitleById($cat);
            if (!empty($category)) {
                $this->breadcrumb->append($category);
            }
        }
    }
}
