<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\View\Block\Context\ListingBlockContext;

abstract class AbstractListingBlock extends AbstractBlock implements ListingBlockInterface
{
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;

    /**
     * AbstractListingTemplate constructor.
     * @param ListingBlockContext $context
     */
    public function __construct(ListingBlockContext $context)
    {
        parent::__construct($context);

        $this->resultsPerPage = $context->getResultsPerPage();
        $this->pagination = $context->getPagination();
    }

    /**
     * @return string
     */
    abstract protected function getModuleName(): string;

    /**
     * @return int
     */
    abstract protected function getTotalResults(): int;

    /**
     * @param int $resultsPerPage
     * @return array
     */
    abstract protected function getResults(int $resultsPerPage): array;

    /**
     * @return int
     */
    protected function getResultsPerPage(): int
    {
        return $this->resultsPerPage->getResultsPerPage($this->getModuleName());
    }

    /**
     * @param int $resultsPerPage
     */
    protected function configurePagination(int $resultsPerPage)
    {
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->getTotalResults());
    }
}
