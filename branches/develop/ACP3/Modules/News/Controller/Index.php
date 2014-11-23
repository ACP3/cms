<?php

namespace ACP3\Modules\News\Controller;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\News;

/**
 * Class Index
 * @package ACP3\Modules\News\Controller
 */
class Index extends Core\Modules\Controller\Frontend
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
     * @var Core\Config
     */
    protected $newsConfig;
    /**
     * @var Categories\Model
     */
    protected $categoriesModel;

    /**
     * @param Core\Context\Frontend $context
     * @param Core\Date $date
     * @param Core\Pagination $pagination
     * @param News\Model $newsModel
     * @param News\Cache $newsCache
     * @param Core\Config $newsConfig
     * @param Categories\Model $categoriesModel
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        News\Model $newsModel,
        News\Cache $newsCache,
        Core\Config $newsConfig,
        Categories\Model $categoriesModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->newsModel = $newsModel;
        $this->newsCache = $newsCache;
        $this->newsConfig = $newsConfig;
        $this->categoriesModel = $categoriesModel;
    }

    public function actionDetails()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true &&
            $this->newsModel->resultExists($this->request->id, $this->date->getCurrentDateTime()) == 1) {
            $settings = $this->newsConfig->getSettings();

            $news = $this->newsCache->getCache($this->request->id);

            $this->breadcrumb->append($this->lang->t('news', 'news'), 'news');

            if ($settings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
            }
            $this->breadcrumb->append($news['title']);

            if (!empty($news['uri']) && (bool)preg_match('=^http(s)?://=', $news['uri']) === false) {
                $news['uri'] = 'http://' . $news['uri'];
            }
            $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

            $this->view->assign('news', $news);
            $this->view->assign('dateformat', $settings['dateformat']);

            if ($settings['comments'] == 1 && $news['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true) {
                /** @var \ACP3\Modules\Comments\Controller\Index $comments */
                $comments = $this->get('comments.controller.frontend.index');
                $comments
                    ->setModule('news')
                    ->setEntryId($this->request->id);

                $this->view->assign('comments', $comments->actionIndex());
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        if (isset($_POST['cat']) && $this->get('core.validator.rules.misc')->isNumber($_POST['cat']) === true) {
            $cat = (int)$_POST['cat'];
        } elseif ($this->get('core.validator.rules.misc')->isNumber($this->request->cat) === true) {
            $cat = (int)$this->request->cat;
        } else {
            $cat = 0;
        }

        if ($this->modules->isActive('categories') === true) {
            $this->view->assign('categories', $this->get('categories.helpers')->categoriesList('news', $cat));
        }

        $settings = $this->newsConfig->getSettings();
        // Kategorie in Brotkrümelspur anzeigen
        if ($cat !== 0 && $settings['category_in_breadcrumb'] == 1) {
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
            // Überprüfen, ob das Kommentare Modul aktiv ist
            $commentsCheck = $this->modules->isActive('comments');

            $this->pagination->setTotalResults($this->newsModel->countAll($time, $cat));
            $this->pagination->display();

            $formatter = $this->get('core.helpers.stringFormatter');
            for ($i = 0; $i < $c_news; ++$i) {
                if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && $commentsCheck === true) {
                    $news[$i]['comments_count'] = $this->get('comments.helpers')->commentsCount('news', $news[$i]['id']);
                }
                if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                    $news[$i]['text'] = $formatter->shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . $this->router->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->lang->t('news', 'readmore') . "]</a>\n");
                }
            }
            $this->view->assign('news', $news);
            $this->view->assign('dateformat', $settings['dateformat']);
        }
    }

}