<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Articles\Controller\Frontend\Index
 */
class Details extends Core\Controller\FrontendController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
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
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext       $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Core\Helpers\PageBreaks                       $pageBreaksHelper
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                   $articlesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\PageBreaks $pageBreaksHelper,
        Articles\Model\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pageBreaksHelper = $pageBreaksHelper;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
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
