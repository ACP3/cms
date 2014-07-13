<?php

namespace ACP3\Modules\News\Controller;

use ACP3\Core;
use ACP3\Modules\News;

/**
 * Class Index
 * @package ACP3\Modules\News\Controller
 */
class Index extends Core\Modules\Controller
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
     * @var News\Model
     */
    protected $newsModel;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        News\Model $newsModel)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->date = $date;
        $this->db = $db;
        $this->newsModel = $newsModel;
    }

    public function actionDetails()
    {
        if ($this->get('core.validate')->isNumber($this->uri->id) === true && $this->newsModel->resultExists($this->uri->id, $this->date->getCurrentDateTime()) == 1) {
            $config = new Core\Config($this->db, 'news');
            /** @var Core\Helpers\StringFormatter $formatter */
            $formatter = $this->get('core.helpers.string.formatter');
            $settings = $config->getSettings();

            $cache = new News\Cache($this->newsModel);
            $news = $cache->getCache($this->uri->id);

            $this->breadcrumb->append($this->lang->t('news', 'news'), 'news');

            if ($settings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
            }
            $this->breadcrumb->append($news['title']);

            $news['date_formatted'] = $this->date->format($news['start'], $settings['dateformat']);
            $news['date_iso'] = $this->date->format($news['start'], 'c');
            $news['text'] = $formatter->rewriteInternalUri($news['text']);
            if (!empty($news['uri']) && (bool)preg_match('=^http(s)?://=', $news['uri']) === false) {
                $news['uri'] = 'http://' . $news['uri'];
            }
            $news['target'] = $news['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

            $this->view->assign('news', $news);

            if ($settings['comments'] == 1 && $news['comments'] == 1 && $this->modules->hasPermission('frontend/comments') === true) {
                $comments = new \ACP3\Modules\Comments\Controller\Index(
                    $this->auth,
                    $this->breadcrumb,
                    $this->date,
                    $this->db,
                    $this->lang,
                    $this->session,
                    $this->uri,
                    $this->view,
                    $this->seo,
                    'news',
                    $this->uri->id
                );
                $this->view->assign('comments', $comments->actionIndex());
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        if (isset($_POST['cat']) && $this->get('core.validate')->isNumber($_POST['cat']) === true) {
            $cat = (int)$_POST['cat'];
        } elseif ($this->get('core.validate')->isNumber($this->uri->cat) === true) {
            $cat = (int)$this->uri->cat;
        } else {
            $cat = 0;
        }

        if ($this->modules->isActive('categories') === true) {
            $this->view->assign('categories', $this->get('categories.helpers')->categoriesList('news', $cat));
        }

        $config = new Core\Config($this->db, 'news');
        $settings = $config->getSettings();
        // Kategorie in Brotkrümelspur anzeigen
        if ($cat !== 0 && $settings['category_in_breadcrumb'] == 1) {
            $this->seo->setCanonicalUri($this->uri->route('news'));
            $this->breadcrumb->append($this->lang->t('news', 'news'), 'news');
            $category = $this->db->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($cat));
            if (!empty($category)) {
                $this->breadcrumb->append($category);
            }
        }

        $time = $this->date->getCurrentDateTime();
        // Falls Kategorie angegeben, News nur aus eben jener selektieren
        if (!empty($cat)) {
            $news = $this->newsModel->getAllByCategoryId($cat, $time, POS, $this->auth->entries);
        } else {
            $news = $this->newsModel->getAll($time, POS, $this->auth->entries);
        }
        $c_news = count($news);

        if ($c_news > 0) {
            $commentsCheck = false;
            // Überprüfen, ob das Kommentare Modul aktiv ist
            if ($this->modules->isActive('comments') === true) {
                $commentsCheck = true;
            }

            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->newsModel->countAll($time, $cat)
            );
            $pagination->display();

            $formatter = $this->get('core.helpers.string.formatter');
            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['date_formatted'] = $this->date->format($news[$i]['start'], $settings['dateformat']);
                $news[$i]['date_iso'] = $this->date->format($news[$i]['start'], 'c');
                $news[$i]['text'] = $formatter->rewriteInternalUri($news[$i]['text']);
                if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && $commentsCheck === true) {
                    $news[$i]['comments_count'] = $this->get('comments.helpers')->commentsCount('news', $news[$i]['id']);
                }
                if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                    $news[$i]['text'] = $formatter->shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . $this->uri->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->lang->t('news', 'readmore') . "]</a>\n");
                }
            }
            $this->view->assign('news', $news);
        }
    }

}
