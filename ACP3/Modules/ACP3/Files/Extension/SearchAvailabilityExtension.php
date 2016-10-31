<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Extension;


use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\SearchResultsAwareRepository;
use ACP3\Modules\ACP3\Search\Extension\AbstractSearchAvailabilityExtension;

class SearchAvailabilityExtension extends AbstractSearchAvailabilityExtension
{
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;

    /**
     * SearchAvailabilityExtension constructor.
     * @param RouterInterface $router
     * @param SearchResultsAwareRepository $repository
     */
    public function __construct(
        RouterInterface $router,
        SearchResultsAwareRepository $repository
    ) {
        parent::__construct($repository);

        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @param string $searchTerm
     * @param string $areas
     * @param string $sortDirection
     * @return array
     */
    public function fetchSearchResults($searchTerm, $areas, $sortDirection)
    {
        $results = parent::fetchSearchResults($searchTerm, $areas, $sortDirection);
        $cResults = count($results);

        for ($i = 0; $i < $cResults; ++$i) {
            $results[$i]['hyperlink'] = $this->router->route('files/index/details/id_' . $results[$i]['id']);
        }

        return $results;
    }

    /**
     * @param string $areas
     *
     * @return string
     */
    protected function mapSearchAreasToFields($areas)
    {
        switch ($areas) {
            case 'title':
                return 'title, file';
            case 'content':
                return 'text';
            default:
                return 'title, file, text';
        }
    }
}
