<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewslettersRepository;

class NewsletterArchiveListingBlock extends AbstractListingBlock
{
    /**
     * @var NewslettersRepository
     */
    private $newsletterRepository;

    /**
     * NewsletterArchiveListingBlock constructor.
     * @param ListingBlockContext $context
     * @param NewslettersRepository $newsletterRepository
     */
    public function __construct(ListingBlockContext $context, NewslettersRepository $newsletterRepository)
    {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @inheritdoc
     */
    protected function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalResults(): int
    {
        return $this->newsletterRepository->countAll(AccountStatus::ACCOUNT_STATUS_CONFIRMED);
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        return $this->newsletterRepository->getAll(
            AccountStatus::ACCOUNT_STATUS_CONFIRMED,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $resultsPerPage = $this->getTotalResults();
        $this->configurePagination($resultsPerPage);

        return [
            'newsletters' => $this->getResults($resultsPerPage),
            'pagination' => $this->pagination->render(),
        ];
    }
}