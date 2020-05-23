<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository;

class NewsletterArchiveViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository
     */
    private $newsletterRepository;
    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        NewsletterRepository $newsletterRepository,
        Pagination $pagination,
        ResultsPerPage $resultsPerPage
    ) {
        $this->newsletterRepository = $newsletterRepository;
        $this->pagination = $pagination;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(NewsletterSchema::MODULE_NAME);
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->newsletterRepository->countAll(1));

        return [
            'newsletters' => $this->newsletterRepository->getAll(
                AccountStatus::ACCOUNT_STATUS_CONFIRMED,
                $this->pagination->getResultsStartOffset(),
                $resultsPerPage
            ),
            'pagination' => $this->pagination->render(),
        ];
    }
}
