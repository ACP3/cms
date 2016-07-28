<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Date;
use ACP3\Core\I18n\Translator;
use ACP3\Core\RouterInterface;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Search\Event\SearchResultsEvent;

/**
 * Class OnDisplaySearchResultsListener
 * @package ACP3\Modules\ACP3\Articles\Event\Listener
 */
class OnDisplaySearchResultsListener
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
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    private $articleRepository;

    /**
     * OnDisplaySearchResultsListener constructor.
     *
     * @param \ACP3\Core\ACL                                      $acl
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Core\I18n\Translator                          $translator
     * @param \ACP3\Core\RouterInterface                          $router
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository $articleRepository
     */
    public function __construct(
        ACL $acl,
        Date $date,
        Translator $translator,
        RouterInterface $router,
        ArticleRepository $articleRepository
    ) {
        $this->acl = $acl;
        $this->date = $date;
        $this->translator = $translator;
        $this->router = $router;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Search\Event\SearchResultsEvent $displaySearchResults
     */
    public function onDisplaySearchResults(SearchResultsEvent $displaySearchResults)
    {
        if (in_array('articles', $displaySearchResults->getModules())
            && $this->acl->hasPermission('frontend/articles') === true
        ) {
            $fields = $this->mapSearchAreasToFields($displaySearchResults->getAreas());

            $results = $this->articleRepository->getAllSearchResults(
                $fields,
                $displaySearchResults->getSearchTerm(),
                $displaySearchResults->getSortDirection(),
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

                $displaySearchResults->addSearchResultsByModule(
                    $this->translator->t('articles', 'articles'),
                    $searchResults
                );
            }
        }
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
