<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Articles\Controller\Frontend\Index
 */
class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\PageBreaks
     */
    protected $pageBreaksHelper;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
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
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                   $articlesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\PageBreaks $pageBreaksHelper,
        Articles\Model\Repository\ArticleRepository $articleRepository,
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
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        if ($this->articleRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);

            $article = $this->articlesCache->getCache($id);
            
            $this->breadcrumb->replaceAncestor($article['title'], '', true);

            return [
                'page' => $this->pageBreaksHelper->splitTextIntoPages(
                    $this->view->fetchStringAsTemplate($article['text']),
                    $this->request->getUriWithoutPages()
                )
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
