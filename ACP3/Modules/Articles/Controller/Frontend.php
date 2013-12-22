<?php

namespace ACP3\Modules\Articles\Controller;

use ACP3\Core;
use ACP3\Modules\Articles;

/**
 * Module controller of the articles frontend
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

        $this->model = new Articles\Model($this->db);
    }

    public function actionList()
    {
        $time = $this->date->getCurrentDateTime();

        $articles = $this->model->getAll($time, POS, $this->auth->entries);
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $this->view->assign('pagination', Core\Functions::pagination($this->model->countAll($time)));

            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['date_formatted'] = $this->date->format($articles[$i]['start']);
                $articles[$i]['date_iso'] = $this->date->format($articles[$i]['start'], 'c');
            }

            $this->view->assign('articles', $articles);
        }
    }

    public function actionDetails()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resultExists($this->uri->id, $this->date->getCurrentDateTime()) === true) {
            $page = Articles\Helpers::getArticlesCache($this->uri->id);

            $this->breadcrumb->replaceAnchestor($page['title'], 0, true);

            $this->view->assign('page', Core\Functions::splitTextIntoPages(Core\Functions::rewriteInternalUri($page['text']), $this->uri->getCleanQuery()));
        } else {
            $this->uri->redirect('errors/404');
        }
    }

}