<?php
namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Date;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router;
use ACP3\Core\RouterInterface;
use ACP3\Modules\ACP3\News\Model\NewsRepository;
use ACP3\Modules\ACP3\Search\Event\DisplaySearchResults;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnDisplaySearchResultsListener
 * @package ACP3\Modules\ACP3\News\Event\Listener
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
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    private $newsRepository;

    /**
     * OnDisplaySearchResultsListener constructor.
     *
     * @param \ACP3\Core\ACL                               $acl
     * @param \ACP3\Core\Date                              $date
     * @param \ACP3\Core\I18n\Translator                   $translator
     * @param \ACP3\Core\RouterInterface                   $router
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository $newsRepository
     */
    public function __construct(
        ACL $acl,
        Date $date,
        Translator $translator,
        RouterInterface $router,
        NewsRepository $newsRepository
    ) {
        $this->acl = $acl;
        $this->date = $date;
        $this->translator = $translator;
        $this->router = $router;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Search\Event\DisplaySearchResults $displaySearchResults
     */
    public function onDisplaySearchResults(DisplaySearchResults $displaySearchResults)
    {
        if (in_array('news', $displaySearchResults->getModules()) && $this->acl->hasPermission('frontend/news')) {
            $fields = $this->mapSearchAreasToFields($displaySearchResults->getAreas());

            $results = $this->newsRepository->getAllSearchResults(
                $fields,
                $displaySearchResults->getSearchTerm(),
                $displaySearchResults->getSortDirection(),
                $this->date->getCurrentDateTime()
            );
            $cResults = count($results);

            if ($cResults > 0) {
                $searchResults = [];
                $searchResults['dir'] = 'news';
                for ($i = 0; $i < $cResults; ++$i) {
                    $searchResults['results'][$i] = $results[$i];
                    $searchResults['results'][$i]['hyperlink'] = $this->router->route('news/index/details/id_' . $results[$i]['id']);
                }

                $displaySearchResults->addSearchResultsByModule($this->translator->t('news', 'news'), $searchResults);
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
