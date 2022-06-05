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
use ACP3\Modules\ACP3\Newsletter\Repository\NewsletterRepository;

class NewsletterArchiveViewProvider
{
    public function __construct(private readonly NewsletterRepository $newsletterRepository, private readonly Pagination $pagination, private readonly ResultsPerPage $resultsPerPage)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
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
