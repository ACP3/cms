<?php
namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Date;
use ACP3\Core\Lang;
use ACP3\Core\Router;
use ACP3\Modules\ACP3\Articles\Model\ArticleRepository;
use ACP3\Modules\ACP3\Search\Event\DisplaySearchResults;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnDisplaySearchResultsListener
 * @package ACP3\Modules\ACP3\Articles\Event\Listener
 */
class OnDisplaySearchResultsListener extends Event
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
     * @var \ACP3\Core\Lang
     */
    private $lang;
    /**
     * @var \ACP3\Core\Router
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    private $articleRepository;

    /**
     * @param \ACP3\Core\ACL                                      $acl
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Core\Lang                                     $lang
     * @param \ACP3\Core\Router                                   $router
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     */
    public function __construct(
        ACL $acl,
        Date $date,
        Lang $lang,
        Router $router,
        ArticleRepository $articleRepository
    )
    {
        $this->acl = $acl;
        $this->date = $date;
        $this->lang = $lang;
        $this->router = $router;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Search\Event\DisplaySearchResults $displaySearchResults
     */
    public function onDisplaySearchResults(DisplaySearchResults $displaySearchResults)
    {
        if (in_array('articles', $displaySearchResults->getModules()) && $this->acl->hasPermission('frontend/articles')) {
            $fields = $this->mapSearchAreasToFields($displaySearchResults->getAreas());

            $results = $this->articleRepository->getAllSearchResults(
                $fields,
                $displaySearchResults->getSearchTerm(),
                $displaySearchResults->getSortDirection(),
                $this->date->getCurrentDateTime()
            );
            $c_results = count($results);

            if ($c_results > 0) {
                $searchResults = [];
                $searchResults['dir'] = 'articles';
                for ($i = 0; $i < $c_results; ++$i) {
                    $searchResults['results'][$i] = $results[$i];
                    $searchResults['results'][$i]['hyperlink'] = $this->router->route('articles/index/details/id_' . $results[$i]['id']);
                }

                $displaySearchResults->addSearchResultsByModule($this->lang->t('articles', 'articles'), $searchResults);
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