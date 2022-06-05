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
use ACP3\Modules\ACP3\Articles\Repository\ArticleRepository;

class ArticleListViewProvider
{
    public function __construct(private readonly ArticleRepository $articleRepository, private readonly Date $date, private readonly Pagination $pagination, private readonly ResultsPerPage $resultsPerPage)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
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
