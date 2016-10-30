<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Extension;


use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Search\Extension\SearchAvailabilityExtensionInterface;

class SearchAvailabilityExtension implements SearchAvailabilityExtensionInterface
{
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
     * @param Date $date
     * @param RouterInterface $router
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        Date $date,
        RouterInterface $router,
        ArticleRepository $articleRepository
    ) {
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
        $fields = $this->mapSearchAreasToFields($areas);

        $results = $this->articleRepository->getAllSearchResults(
            $fields,
            $searchTerm,
            $sortDirection,
            $this->date->getCurrentDateTime()
        );
        $cResults = count($results);

        for ($i = 0; $i < $cResults; ++$i) {
            $results[$i]['hyperlink'] = $this->router->route('articles/index/details/id_' . $results[$i]['id']);
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
                return 'title';
            case 'content':
                return 'text';
            default:
                return 'title, text';
        }
    }
}
