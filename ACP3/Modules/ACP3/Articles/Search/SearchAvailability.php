<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Search;


use ACP3\Core\ACL;
use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
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
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    private $articleRepository;

    /**
     * SearchAvailability constructor.
     * @param ACL $acl
     * @param Date $date
     * @param RouterInterface $router
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        ACL $acl,
        Date $date,
        RouterInterface $router,
        ArticleRepository $articleRepository
    ) {
        $this->acl = $acl;
        $this->date = $date;
        $this->router = $router;
        $this->articleRepository = $articleRepository;
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
        if ($this->acl->hasPermission('frontend/articles') === true) {
            $fields = $this->mapSearchAreasToFields($areas);

            $results = $this->articleRepository->getAllSearchResults(
                $fields,
                $searchTerm,
                $sortDirection,
                $this->date->getCurrentDateTime()
            );
            $cResults = count($results);

            if ($cResults > 0) {
                $searchResults = [];
                $searchResults['dir'] = 'articles';
                for ($i = 0; $i < $cResults; ++$i) {
                    $searchResults['results'][$i] = $results[$i];
                    $searchResults['results'][$i]['hyperlink'] = $this->router->route(
                        'articles/index/details/id_' . $results[$i]['id']
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
