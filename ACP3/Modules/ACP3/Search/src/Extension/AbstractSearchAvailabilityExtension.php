<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Extension;

use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Search\Repository\SearchResultsAwareRepositoryInterface;

abstract class AbstractSearchAvailabilityExtension implements SearchAvailabilityExtensionInterface
{
    public function __construct(protected RouterInterface $router, protected SearchResultsAwareRepositoryInterface $repository)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function fetchSearchResults(string $searchTerm, string $areas, string $sortDirection): array
    {
        $results = $this->repository->getAllSearchResults(
            $this->mapSearchAreasToFields($areas),
            $searchTerm,
            $sortDirection
        );

        foreach ($results as $i => $iValue) {
            $results[$i]['hyperlink'] = $this->router->route(sprintf($this->getRouteName(), $iValue['id']));
        }

        return $results;
    }

    abstract protected function mapSearchAreasToFields(string $area): string;

    abstract protected function getRouteName(): string;
}
