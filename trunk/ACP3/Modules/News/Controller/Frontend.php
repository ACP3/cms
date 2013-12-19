<?php

namespace ACP3\Modules\News\Controller;

use ACP3\Core;
use ACP3\Modules\News;

/**
 * Description of NewsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);

        $this->model = new News\Model($this->db);
    }

    public function actionDetails()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resultExists($this->uri->id, $this->date->getCurrentDateTime()) == 1) {

            $settings = Core\Config::getSettings('news');
            $news = $this->model->getNewsCache($this->uri->id);

            $this->breadcrumb->append($this->lang->t('news', 'news'), $this->uri->route('news'));

            if ($settings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], $this->uri->route('news/list/cat_' . $news['category_id']));
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

            if ($settings['comments'] == 1 && $news['comments'] == 1 && Core\Modules::hasPermission('comments', 'list') === true) {
                $comments = new \ACP3\Modules\Comments\Controller\Frontend(
                    $this->auth,
                    $this->breadcrumb,
                    $this->date,
                    $this->db,
                    $this->lang,
                    $this->session,
                    $this->uri,
                    $this->view,
                    'news',
                    $this->uri->id
                );
                $this->view->assign('comments', $comments->actionList());
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
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
            Core\SEO::setCanonicalUri($this->uri->route('news'));
            $this->breadcrumb->append($this->lang->t('news', 'news'), $this->uri->route('news'));
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

            $this->view->assign('pagination', Core\Functions::pagination($this->model->countAll($time, $cat)));

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

    public function actionSidebar()
    {
        $settings = Core\Config::getSettings('news');

        $news = $this->model->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_news = count($news);

        if ($c_news > 0) {
            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['start'] = $this->date->format($news[$i]['start'], $settings['dateformat']);
                $news[$i]['title'] = $news[$i]['title'];
                $news[$i]['title_short'] = Core\Functions::shortenEntry($news[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_news', $news);
        }

        $this->view->displayTemplate('news/sidebar.tpl');
    }

}
