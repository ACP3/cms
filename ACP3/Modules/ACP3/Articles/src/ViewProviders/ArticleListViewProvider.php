<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Modules\ACP3\Articles\Installer\Schema as ArticlesSchema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;

class ArticleListViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    private $articleRepository;
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        ArticleRepository $articleRepository,
        Date $date,
        Pagination $pagination,
        ResultsPerPage $resultsPerPage
    ) {
        $this->articleRepository = $articleRepository;
        $this->date = $date;
        $this->pagination = $pagination;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Pagination\Exception\InvalidPageException
     */
    public function __invoke(): array
    {
        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(ArticlesSchema::MODULE_NAME);
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
            'pagination' => $this->pagination->render(),
        ];
    }
}
