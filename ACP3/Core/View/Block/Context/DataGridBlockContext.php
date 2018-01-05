<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block\Context;

use ACP3\Core\Helpers\ResultsPerPage;
use Psr\Container\ContainerInterface;

class DataGridBlockContext extends BlockContext
{
    /**
     * @var ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DataGridBlockContext constructor.
     * @param BlockContext $context
     * @param ResultsPerPage $resultsPerPage
     * @param ContainerInterface $container
     */
    public function __construct(
        BlockContext $context,
        ResultsPerPage $resultsPerPage,
        ContainerInterface $container
    ) {
        parent::__construct(
            $context->getView(),
            $context->getBreadcrumb(),
            $context->getTitle(),
            $context->getTranslator()
        );

        $this->resultsPerPage = $resultsPerPage;
        $this->container = $container;
    }

    /**
     * @return ResultsPerPage
     */
    public function getResultsPerPage(): ResultsPerPage
    {
        return $this->resultsPerPage;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
