<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Extension;

use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Search\Model\Repository\SearchResultsAwareRepositoryInterface;

abstract class AbstractSearchAvailabilityExtension implements SearchAvailabilityExtensionInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * @var SearchResultsAwareRepositoryInterface
     */
    protected $repository;

    public function __construct(
        RouterInterface $router,
        SearchResultsAwareRepositoryInterface $repository
    ) {
        $this->router = $router;
        $this->repository = $repository;
    }

    /**
     * @param string $searchTerm
     * @param string $areas
     * @param string $sortDirection
     *
     * @return array
     */
    public function fetchSearchResults($searchTerm, $areas, $sortDirection)
    {
        $results = $this->repository->getAllSearchResults(
            $this->mapSearchAreasToFields($areas),
            $searchTerm,
            $sortDirection
        );
        $cResults = \count($results);

        for ($i = 0; $i < $cResults; ++$i) {
            $results[$i]['hyperlink'] = $this->router->route(sprintf($this->getRouteName(), $results[$i]['id']));
        }

        return $results;
    }

    /**
     * @param string $area
     *
     * @return string
     */
    abstract protected function mapSearchAreasToFields($area);

    /**
     * @return string
     */
    abstract protected function getRouteName();
}
