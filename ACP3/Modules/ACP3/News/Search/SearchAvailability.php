<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Search;


use ACP3\Core\ACL;
use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;
use ACP3\Modules\ACP3\Search\Utility\SearchAvailabilityInterface;

class SearchAvailability implements SearchAvailabilityInterface
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    private $newsRepository;

    /**
     * OnDisplaySearchResultsListener constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Router\RouterInterface $router
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository $newsRepository
     */
    public function __construct(
        ACL $acl,
        Date $date,
        RouterInterface $router,
        NewsRepository $newsRepository
    ) {
        $this->acl = $acl;
        $this->date = $date;
        $this->router = $router;
        $this->newsRepository = $newsRepository;
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
        if ($this->acl->hasPermission('frontend/news') === true) {
            $fields = $this->mapSearchAreasToFields($areas);

            $results = $this->newsRepository->getAllSearchResults(
                $fields,
                $searchTerm,
                $sortDirection,
                $this->date->getCurrentDateTime()
            );
            $cResults = count($results);

            if ($cResults > 0) {
                $searchResults = [];
                $searchResults['dir'] = $this->getModuleName();
                for ($i = 0; $i < $cResults; ++$i) {
                    $searchResults['results'][$i] = $results[$i];
                    $searchResults['results'][$i]['hyperlink'] = $this->router->route(
                        'news/index/details/id_' . $results[$i]['id']
                    );
                }

                return $searchResults;
            }
        }

        return [];
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
                return 'title';
            case 'content':
                return 'text';
            default:
                return 'title, text';
        }
    }
}
