<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\View\Block\Frontend;

use ACP3\Core\Date;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Categories\Helpers;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\News\Controller\Admin\Index\CommentsHelperTrait;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class NewsListingBlock extends AbstractListingBlock
{
    use CommentsHelperTrait;

    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Date
     */
    private $date;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CategoriesRepository
     */
    private $categoryRepository;
    /**
     * @var NewsRepository
     */
    private $newsRepository;
    /**
     * @var Helpers
     */
    private $categoriesHelpers;
    /**
     * @var StringFormatter
     */
    private $stringFormatter;
    /**
     * @var MetaStatements
     */
    private $metaStatements;

    /**
     * NewsListingBlock constructor.
     * @param ListingBlockContext $context
     * @param SettingsInterface $settings
     * @param Date $date
     * @param RouterInterface $router
     * @param StringFormatter $stringFormatter
     * @param NewsRepository $newsRepository
     * @param CategoriesRepository $categoryRepository
     * @param Helpers $categoriesHelpers
     */
    public function __construct(
        ListingBlockContext $context,
        SettingsInterface $settings,
        Date $date,
        RouterInterface $router,
        StringFormatter $stringFormatter,
        NewsRepository $newsRepository,
        CategoriesRepository $categoryRepository,
        Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->settings = $settings;
        $this->date = $date;
        $this->router = $router;
        $this->newsRepository = $newsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->stringFormatter = $stringFormatter;
    }

    /**
     * @param MetaStatements $metaStatements
     */
    public function setMetaStatements(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    /**
     * @inheritdoc
     */
    protected function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalResults(): int
    {
        $data = $this->getData();

        return $this->newsRepository->countAll($this->date->getCurrentDateTime(), $data['category_id']);
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        $data = $this->getData();
        $news = $this->fetchNews($resultsPerPage, $data['category_id'], $this->date->getCurrentDateTime());
        $cNews = count($news);

        $settings = $this->getNewsSettings();

        for ($i = 0; $i < $cNews; ++$i) {
            $news[$i]['text'] = $this->view->fetchStringAsTemplate($news[$i]['text']);
            if ($settings['comments'] == 1 && $news[$i]['comments'] == 1) {
                $news[$i]['comments_count'] = $this->commentsHelpers->commentsCount(
                    $this->getModuleName(),
                    $news[$i]['id']
                );
            }
            if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                $news[$i]['text'] = $this->addReadMoreLink($news[$i]);
            }
        }

        return $news;
    }

    /**
     * @return array
     */
    private function getNewsSettings(): array
    {
        return $this->settings->getSettings($this->getModuleName());
    }

    /**
     * @param int $resultsPerPage
     * @param int $categoryId
     * @param string $time
     *
     * @return array
     */
    private function fetchNews(int $resultsPerPage, int $categoryId, $time): array
    {
        if (!empty($categoryId)) {
            return $this->newsRepository->getAllByCategoryId(
                $categoryId,
                $time,
                $this->pagination->getResultsStartOffset(),
                $resultsPerPage
            );
        }

        return $this->newsRepository->getAll(
            $time,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
    }

    /**
     * @param array $news
     *
     * @return string
     */
    private function addReadMoreLink(array $news)
    {
        $readMoreLink = '...<a href="' . $this->router->route('news/details/id_' . $news['id']) . '">[';
        $readMoreLink .= $this->translator->t('news', 'readmore') . "]</a>\n";

        return $this->stringFormatter->shortenEntry(
            $news['text'],
            $this->getNewsSettings()['readmore_chars'],
            50,
            $readMoreLink
        );
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->addBreadcrumbStep($data['category_id']);

        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        return [
            'news' => $this->getResults($resultsPerPage),
            'dateformat' => $this->getNewsSettings()['dateformat'],
            'categories' => $this->categoriesHelpers->categoriesList('news', $data['category_id']),
            'pagination' => $this->pagination->render()
        ];
    }

    /**
     * @param int $categoryId
     */
    private function addBreadcrumbStep(int $categoryId)
    {
        if ($categoryId !== 0 && $this->getNewsSettings()['category_in_breadcrumb'] == 1) {
            if ($this->metaStatements) {
                $this->metaStatements->setCanonicalUri($this->router->route('news'));
            }

            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
            $category = $this->categoryRepository->getTitleById($categoryId);
            if (!empty($category)) {
                $this->breadcrumb->append($category);
            }
        }
    }
}
