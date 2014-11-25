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
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Articles\Model
     */
    protected $articlesModel;
    /**
     * @var Articles\Cache
     */
    protected $articlesCache;

    /**
     * @param Core\Context\Frontend $context
     * @param Core\Date $date
     * @param Core\Pagination $pagination
     * @param Articles\Model $articlesModel
     * @param Articles\Cache $articlesCache
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Articles\Model $articlesModel,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->articlesModel = $articlesModel;
        $this->articlesCache = $articlesCache;
    }

    public function actionIndex()
    {
        $time = $this->date->getCurrentDateTime();

        $articles = $this->articlesModel->getAll($time, POS, $this->auth->entries);
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $this->pagination->setTotalResults($this->articlesModel->countAll($time));
            $this->pagination->display();

            $this->view->assign('articles', $articles);
        }
    }

    public function actionDetails()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true &&
            $this->articlesModel->resultExists($this->request->id, $this->date->getCurrentDateTime()) === true) {
            $article = $this->articlesCache->getCache($this->request->id);

            $this->breadcrumb->replaceAnchestor($article['title'], 0, true);

            $toc = $this->get('core.helpers.toc');
            $this->view->assign('page', $toc->splitTextIntoPages($article['text'], $this->request->getUriWithoutPages()));
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}
