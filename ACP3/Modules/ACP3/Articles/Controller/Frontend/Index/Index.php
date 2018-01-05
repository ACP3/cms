<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    protected $articleRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext       $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Core\Pagination                               $pagination
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository $articleRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Articles\Model\Repository\ArticleRepository $articleRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Articles\Installer\Schema::MODULE_NAME);
        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->articleRepository->countAll($time));

        $articles = $this->articleRepository->getAll(
            $time,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
        return [
            'articles' => $articles,
            'pagination' => $this->pagination->render()
        ];
    }
}
