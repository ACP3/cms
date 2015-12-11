<?php

namespace ACP3\Modules\ACP3\News\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\News\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var News\Cache
     */
    protected $newsCache;
    /**
     * @var array
     */
    protected $newsSettings;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var bool
     */
    protected $commentsActive;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext          $context
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Pagination                                  $pagination
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository           $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache                          $newsCache
     * @param \ACP3\Modules\ACP3\Categories\Helpers                  $categoriesHelpers
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        News\Model\NewsRepository $newsRepository,
        News\Cache $newsCache,
        Categories\Helpers $categoriesHelpers,
        Categories\Model\CategoryRepository $categoryRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->categoryRepository = $categoryRepository;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->newsSettings = $this->config->getSettings('news');
        $this->commentsActive = ($this->newsSettings['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true);
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDetails($id)
    {
        if ($this->newsRepository->resultExists($id, $this->date->getCurrentDateTime()) == 1) {
            $news = $this->newsCache->getCache($id);

            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');

            if ($this->newsSettings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
            }
            $this->breadcrumb->append($news['title']);

            $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

            return [
                'news' => $news,
                'dateformat' => $this->newsSettings['dateformat'],
                'comments_allowed' => $this->commentsActive === true && $news['comments'] == 1
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param int $cat
     *
     * @return array
     */
    public function actionIndex($cat = 0)
    {
        // Kategorie in Brotkrümelspur anzeigen
        if ($cat !== 0 && $this->newsSettings['category_in_breadcrumb'] == 1) {
            $this->seo->setCanonicalUri($this->router->route('news'));
            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
            $category = $this->categoryRepository->getTitleById($cat);
            if (!empty($category)) {
                $this->breadcrumb->append($category);
            }
        }

        $time = $this->date->getCurrentDateTime();
        // Falls Kategorie angegeben, News nur aus eben dieser selektieren
        if (!empty($cat)) {
            $news = $this->newsRepository->getAllByCategoryId($cat, $time, POS, $this->user->getEntriesPerPage());
        } else {
            $news = $this->newsRepository->getAll($time, POS, $this->user->getEntriesPerPage());
        }
        $c_news = count($news);

        if ($c_news > 0) {
            $this->pagination->setTotalResults($this->newsRepository->countAll($time, $cat));
            $this->pagination->display();

            $formatter = $this->get('core.helpers.stringFormatter');
            for ($i = 0; $i < $c_news; ++$i) {
                if ($this->commentsActive === true && $news[$i]['comments'] == 1) {
                    $news[$i]['comments_count'] = $this->get('comments.helpers')->commentsCount('news', $news[$i]['id']);
                }
                if ($this->newsSettings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                    $news[$i]['text'] = $formatter->shortenEntry($news[$i]['text'],
                        $this->newsSettings['readmore_chars'], 50,
                        '...<a href="' . $this->router->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->translator->t('news',
                            'readmore') . "]</a>\n");
                }
            }
            $this->view->assign('news', $news);
            $this->view->assign('dateformat', $this->newsSettings['dateformat']);
        }

        return [
            'categories' => $this->categoriesHelpers->categoriesList('news', $cat)
        ];
    }
}
