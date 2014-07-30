<?php

namespace ACP3\Modules\Articles\Controller;

use ACP3\Core;
use ACP3\Modules\Articles;

/**
 * Class Index
 * @package ACP3\Modules\Articles\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var Articles\Model
     */
    protected $articlesModel;

    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Articles\Model $articlesModel)
    {
       parent::__construct($context);

        $this->date = $date;
        $this->articlesModel = $articlesModel;
    }

    public function actionIndex()
    {
        $time = $this->date->getCurrentDateTime();

        $articles = $this->articlesModel->getAll($time, POS, $this->auth->entries);
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->request,
                $this->view,
                $this->articlesModel->countAll($time)
            );
            $pagination->display();

            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['date_formatted'] = $this->date->format($articles[$i]['start']);
                $articles[$i]['date_iso'] = $this->date->format($articles[$i]['start'], 'c');
            }

            $this->view->assign('articles', $articles);
        }
    }

    public function actionDetails()
    {
        if ($this->get('core.validate')->isNumber($this->request->id) === true && $this->articlesModel->resultExists($this->request->id, $this->date->getCurrentDateTime()) === true) {
            $cache = new Articles\Cache($this->articlesModel);
            $article = $cache->getCache($this->request->id);

            $this->breadcrumb->replaceAnchestor($article['title'], 0, true);

            $toc = new Core\Helpers\TableOfContents(
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->request,
                $this->view
            );
            $formatter = $this->get('core.helpers.string.formatter');
            $this->view->assign('page', $toc->splitTextIntoPages($formatter->rewriteInternalUri($article['text']), $this->request->getUriWithoutPages()));
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}