<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block\Context;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;

class ListingBlockContext extends BlockContext
{
    /**
     * @var ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * ListingTemplateContext constructor.
     * @param BlockContext $context
     * @param ResultsPerPage $resultsPerPage
     * @param Pagination $pagination
     */
    public function __construct(BlockContext $context, ResultsPerPage $resultsPerPage, Pagination $pagination)
    {
        parent::__construct(
            $context->getView(),
            $context->getBreadcrumb(),
            $context->getTitle(),
            $context->getTranslator()
        );

        $this->resultsPerPage = $resultsPerPage;
        $this->pagination = $pagination;
    }

    /**
     * @return ResultsPerPage
     */
    public function getResultsPerPage(): ResultsPerPage
    {
        return $this->resultsPerPage;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}
