<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\Extension;


use ACP3\Modules\ACP3\Search\Model\Repository\SearchResultsAwareRepositoryInterface;

abstract class AbstractSearchAvailabilityExtension implements SearchAvailabilityExtensionInterface
{
    /**
     * @var SearchResultsAwareRepositoryInterface
     */
    protected $repository;

    /**
     * AbstractSearchAvailabilityExtension constructor.
     * @param SearchResultsAwareRepositoryInterface $repository
     */
    public function __construct(SearchResultsAwareRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $searchTerm
     * @param string $areas
     * @param string $sortDirection
     * @return array
     */
    public function fetchSearchResults($searchTerm, $areas, $sortDirection)
    {
        return $this->repository->getAllSearchResults(
            $this->mapSearchAreasToFields($areas),
            $searchTerm,
            $sortDirection
        );
    }

    /**
     * @param string $area
     * @return string
     */
    abstract protected function mapSearchAreasToFields($area);
}
