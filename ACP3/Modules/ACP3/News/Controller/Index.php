<?php

namespace ACP3\Modules\ACP3\News\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var News\Model
     */
    protected $newsModel;
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
     * @var Categories\Model
     */
    protected $categoriesModel;
    /**
     * @var bool
     */
    protected $commentsActive;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Core\Pagination                         $pagination
     * @param \ACP3\Modules\ACP3\News\Model                 $newsModel
     * @param \ACP3\Modules\ACP3\News\Cache                 $newsCache
     * @param \ACP3\Modules\ACP3\Categories\Helpers         $categoriesHelpers
     * @param \ACP3\Modules\ACP3\Categories\Model           $categoriesModel
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        News\Model $newsModel,
        News\Cache $newsCache,
        Categories\Helpers $categoriesHelpers,
        Categories\Model $categoriesModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->newsModel = $newsModel;
        $this->newsCache = $newsCache;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->categoriesModel = $categoriesModel;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->newsSettings = $this->config->getSettings('news');
        $this->commentsActive = ($this->newsSettings['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true);
    }

    public function actionDetails()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('id')) === true &&
            $this->newsModel->resultExists($this->request->getParameters()->get('id'), $this->date->getCurrentDateTime()) == 1
        ) {
            $news = $this->newsCache->getCache($this->request->getParameters()->get('id'));

            $this->breadcrumb->append($this->lang->t('news', 'news'), 'news');

            if ($this->newsSettings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
            }
            $this->breadcrumb->append($news['title']);

            $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

            $this->view->assign('news', $news);
            $this->view->assign('dateformat', $this->newsSettings['dateformat']);

            if ($this->commentsActive === true && $news['comments'] == 1) {
                /** @var \ACP3\Modules\ACP3\Comments\Controller\Index $comments */
                $comments = $this->get('comments.controller.frontend.index');
                $comments
                    ->setModule('news')
                    ->setEntryId($this->request->getParameters()->get('id'));

                $this->view->assign('comments', $comments->actionIndex());
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getPost()->get('cat')) === true) {
            $cat = (int)$this->request->getPost()->get('cat');
        } elseif ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('cat')) === true) {
            $cat = (int)$this->request->getParameters()->get('cat');
        } else {
            $cat = 0;
        }

        $this->view->assign('categories', $this->categoriesHelpers->categoriesList('news', $cat));

        // Kategorie in Brotkrümelspur anzeigen
        if ($cat !== 0 && $this->newsSettings['category_in_breadcrumb'] == 1) {
            $this->seo->setCanonicalUri($this->router->route('news'));
            $this->breadcrumb->append($this->lang->t('news', 'news'), 'news');
            $category = $this->categoriesModel->getTitleById($cat);
            if (!empty($category)) {
                $this->breadcrumb->append($category);
            }
        }

        $time = $this->date->getCurrentDateTime();
        // Falls Kategorie angegeben, News nur aus eben dieser selektieren
        if (!empty($cat)) {
            $news = $this->newsModel->getAllByCategoryId($cat, $time, POS, $this->auth->entries);
        } else {
            $news = $this->newsModel->getAll($time, POS, $this->auth->entries);
        }
        $c_news = count($news);

        if ($c_news > 0) {
            $this->pagination->setTotalResults($this->newsModel->countAll($time, $cat));
            $this->pagination->display();

            $formatter = $this->get('core.helpers.stringFormatter');
            for ($i = 0; $i < $c_news; ++$i) {
                if ($this->commentsActive === true && $news[$i]['comments'] == 1) {
                    $news[$i]['comments_count'] = $this->get('comments.helpers')->commentsCount('news', $news[$i]['id']);
                }
                if ($this->newsSettings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                    $news[$i]['text'] = $formatter->shortenEntry($news[$i]['text'], $this->newsSettings['readmore_chars'], 50, '...<a href="' . $this->router->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->lang->t('news', 'readmore') . "]</a>\n");
                }
            }
            $this->view->assign('news', $news);
            $this->view->assign('dateformat', $this->newsSettings['dateformat']);
        }
    }
}
