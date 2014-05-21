<?php

namespace ACP3\Modules\News\Controller;

use ACP3\Core;
use ACP3\Modules\News;

/**
 * Description of NewsFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    /**
     *
     * @var News\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new News\Model($this->db, $this->lang, $this->uri);
    }

    public function actionDetails()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resultExists($this->uri->id, $this->date->getCurrentDateTime()) == 1) {
            $settings = Core\Config::getSettings('news');
            $news = $this->model->getCache($this->uri->id);

            $this->breadcrumb->append($this->lang->t('news', 'news'), 'news');

            if ($settings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
            }
            $this->breadcrumb->append($news['title']);

            $news['date_formatted'] = $this->date->format($news['start'], $settings['dateformat']);
            $news['date_iso'] = $this->date->format($news['start'], 'c');
            $news['text'] = Core\Functions::rewriteInternalUri($news['text']);
            if (!empty($news['uri']) && (bool)preg_match('=^http(s)?://=', $news['uri']) === false) {
                $news['uri'] = 'http://' . $news['uri'];
            }
            $news['target'] = $news['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

            $this->view->assign('news', $news);

            if ($settings['comments'] == 1 && $news['comments'] == 1 && Core\Modules::hasPermission('frontend/comments') === true) {
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
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionIndex()
    {
        if (isset($_POST['cat']) && Core\Validate::isNumber($_POST['cat']) === true) {
            $cat = (int)$_POST['cat'];
        } elseif (Core\Validate::isNumber($this->uri->cat) === true) {
            $cat = (int)$this->uri->cat;
        } else {
            $cat = 0;
        }

        if (Core\Modules::isActive('categories') === true) {
            $this->view->assign('categories', \ACP3\Modules\Categories\Helpers::categoriesList('news', $cat));
        }

        $settings = Core\Config::getSettings('news');
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
            $news = $this->model->getAllByCategoryId($cat, $time, POS, $this->auth->entries);
        } else {
            $news = $this->model->getAll($time, POS, $this->auth->entries);
        }
        $c_news = count($news);

        if ($c_news > 0) {
            $comment_check = false;
            // Überprüfen, ob das Kommentare Modul aktiv ist
            if (Core\Modules::isActive('comments') === true) {
                $comment_check = true;
            }

            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->model->countAll($time, $cat)
            );
            $pagination->display();

            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['date_formatted'] = $this->date->format($news[$i]['start'], $settings['dateformat']);
                $news[$i]['date_iso'] = $this->date->format($news[$i]['start'], 'c');
                $news[$i]['text'] = Core\Functions::rewriteInternalUri($news[$i]['text']);
                if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && $comment_check === true) {
                    $news[$i]['comments_count'] = \ACP3\Modules\Comments\Helpers::commentsCount('news', $news[$i]['id']);
                }
                if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                    $news[$i]['text'] = Core\Functions::shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . $this->uri->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->lang->t('news', 'readmore') . "]</a>\n");
                }
            }
            $this->view->assign('news', $news);
        }
    }

}
