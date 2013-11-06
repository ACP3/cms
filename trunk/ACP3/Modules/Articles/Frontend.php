<?php

namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Module controller of the articles frontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function actionList()
    {
        $period = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
        $time = $this->date->getCurrentDateTime();

        $articles = $this->db->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles WHERE ' . $period . ' ORDER BY title ASC LIMIT ' . POS . ',' . $this->auth->entries, array('time' => $time));
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $this->view->assign('pagination', Core\Functions::pagination($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE ' . $period, array('time' => $time))));

            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['date_formatted'] = $this->date->format($articles[$i]['start']);
                $articles[$i]['date_iso'] = $this->date->format($articles[$i]['start'], 'c');
            }

            $this->view->assign('articles', $articles);
        }
    }

    public function actionDetails()
    {
        $period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';

        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = :id' . $period, array('id' => $this->uri->id, 'time' => $this->date->getCurrentDateTime())) == 1
        ) {
            $page = Helpers::getArticlesCache($this->uri->id);

            $this->breadcrumb->replaceAnchestor($page['title'], 0, true);

            $this->view->assign('page', Core\Functions::splitTextIntoPages(Core\Functions::rewriteInternalUri($page['text']), $this->uri->getCleanQuery()));
        } else {
            $this->uri->redirect('errors/404');
        }
    }

}