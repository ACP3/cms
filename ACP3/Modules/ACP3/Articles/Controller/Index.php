<?php

namespace ACP3\Modules\ACP3\Articles\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Core\Helpers\TableOfContents
     */
    protected $toc;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model
     */
    protected $articlesModel;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;

    /**
     * @param \ACP3\Core\Context\Frontend        $context
     * @param \ACP3\Core\Date                    $date
     * @param \ACP3\Core\Pagination              $pagination
     * @param \ACP3\Core\Helpers\TableOfContents $toc
     * @param \ACP3\Modules\ACP3\Articles\Model       $articlesModel
     * @param \ACP3\Modules\ACP3\Articles\Cache       $articlesCache
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\TableOfContents $toc,
        Articles\Model $articlesModel,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->toc = $toc;
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

            $this->breadcrumb->replaceAncestor($article['title'], 0, true);

            $this->view->assign('page', $this->toc->splitTextIntoPages($article['text'], $this->request->getUriWithoutPages()));
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}
