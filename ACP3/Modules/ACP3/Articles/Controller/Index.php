<?php

namespace ACP3\Modules\ACP3\Articles\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller
 */
class Index extends Core\Modules\FrontendController
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
     * @var \ACP3\Core\Helpers\PageBreaks
     */
    protected $pageBreaksHelper;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext       $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Core\Pagination                               $pagination
     * @param \ACP3\Core\Helpers\PageBreaks                       $pageBreaksHelper
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                   $articlesCache
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\PageBreaks $pageBreaksHelper,
        Articles\Model\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->pageBreaksHelper = $pageBreaksHelper;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
    }

    public function actionIndex()
    {
        $time = $this->date->getCurrentDateTime();

        $articles = $this->articleRepository->getAll($time, POS, $this->user->getEntriesPerPage());
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $this->pagination->setTotalResults($this->articleRepository->countAll($time));
            $this->pagination->display();

            $this->view->assign('articles', $articles);
        }
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDetails($id)
    {
        if ($this->articleRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $article = $this->articlesCache->getCache($id);

            $this->breadcrumb->replaceAncestor($article['title'], 0, true);

            return [
                'page' => $this->pageBreaksHelper->splitTextIntoPages($article['text'], $this->request->getUriWithoutPages())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
