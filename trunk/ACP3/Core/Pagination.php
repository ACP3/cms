<?php
namespace ACP3\Core;

use ACP3\Core\Validator\Rules\Misc;

class Pagination
{
    /**
     * @var Auth
     */
    protected $auth;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var SEO
     */
    protected $seo;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var View
     */
    protected $view;
    /**
     * @var Misc
     */
    protected $miscValidator;
    /**
     * @var int
     */
    private $resultsPerPage = 0;
    /**
     * @var int
     */
    private $totalResults = 0;
    /**
     * @var string
     */
    private $urlFragment = '';
    /**
     * @var int
     */
    private $showFirstLast = 5;
    /**
     * @var int
     */
    private $showPreviousNext = 2;
    /**
     * @var int
     */
    private $pagesToDisplay = 7;
    /**
     * @var int
     */
    private $totalPages = 1;
    /**
     * @var int
     */
    private $currentPage = 1;
    /**
     * @var array
     */
    private $pagination = [];

    function __construct(
        Auth $auth,
        Lang $lang,
        SEO $seo,
        Request $request,
        Router $router,
        View $view,
        Misc $miscValidator)
    {
        $this->auth = $auth;
        $this->lang = $lang;
        $this->seo = $seo;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->miscValidator = $miscValidator;

        $this->resultsPerPage = $auth->entries;
    }

    /**
     * @param $results
     */
    public function setResultsPerPage($results)
    {
        $this->resultsPerPage = (int)$results;
    }

    /**
     * @param $results
     */
    public function setTotalResults($results)
    {
        $this->totalResults = (int)$results;
    }

    /**
     * @param $fragment
     */
    public function setUrlFragment($fragment)
    {
        $this->urlFragment = $fragment;
    }

    /**
     * @param int $pagesToDisplay
     */
    public function setPagesToDisplay($pagesToDisplay)
    {
        $this->pagesToDisplay = (int)$pagesToDisplay;
    }

    /**
     * @param int $showFirstLast
     */
    public function setShowFirstLast($showFirstLast)
    {
        $this->showFirstLast = (int)$showFirstLast;
    }

    /**
     * @param int $showPreviousNext
     */
    public function setShowPreviousNext($showPreviousNext)
    {
        $this->showPreviousNext = (int)$showPreviousNext;
    }

    /**
     * @param string $tplVariable
     *
     * @return string
     */
    public function display($tplVariable = 'pagination')
    {
        $output = '';
        if ($this->totalResults > $this->resultsPerPage) {
            $link = $this->router->route(($this->request->area === 'admin' ? 'acp/' : '') . $this->request->getUriWithoutPages());
            $this->currentPage = $this->miscValidator->isNumber($this->request->page) ? (int)$this->request->page : 1;
            $this->totalPages = (int)ceil($this->totalResults / $this->resultsPerPage);

            $this->setMetaStatements($link);
            $range = $this->calculateRange();

            // Erste Seite
            $this->showFirstPageLink($link, $range);

            // Vorherige Seite
            $this->showPreviousPageLink($link);

            for ($i = (int)$range['start']; $i <= $range['end']; ++$i) {
                $this->pagination[] = $this->buildPageNumber(
                    $this->currentPage === $i,
                    $i,
                    $link . ($i > 1 ? 'page_' . $i . '/' : '') . $this->urlFragment
                );
            }

            // Nächste Seite
            $this->showNextPageLink($link);

            // Letzte Seite
            $this->showLastPageLink($link, $range);

            $this->view->assign('pagination', $this->pagination);

            $output = $this->view->fetchTemplate('system/pagination.tpl');
        }
        $this->view->assign($tplVariable, $output);
    }

    /**
     * @param $link
     */
    private function setMetaStatements($link)
    {
        if ($this->currentPage > 1) {
            $postfix = sprintf($this->lang->t('system', 'page_x'), $this->currentPage);
            $this->breadcrumb->setTitlePostfix($postfix);
        }

        // Vorherige und nächste Seite für Suchmaschinen und Prefetching propagieren
        if ($this->request->area !== 'admin') {
            if ($this->currentPage - 1 > 0) {
                // Seitenangabe in der Seitenbeschreibung ab Seite 2 angeben
                $this->seo->setDescriptionPostfix(sprintf($this->lang->t('system', 'page_x'), $this->currentPage));
                $this->seo->setPreviousPage($link . 'page_' . ($this->currentPage - 1) . '/');
            }
            if ($this->currentPage + 1 <= $this->totalPages) {
                $this->seo->setNextPage($link . 'page_' . ($this->currentPage + 1) . '/');
            }
            if (isset($this->request->page) && $this->request->page === 1) {
                $this->seo->setCanonicalUri($link);
            }
        }
    }

    /**
     * @return array
     */
    private function calculateRange()
    {
        $rangeStart = 1;
        $rangeEnd = $this->totalPages;
        if ($this->totalPages > $this->pagesToDisplay) {
            $center = floor($this->pagesToDisplay / 2);
            // Beginn der anzuzeigenden Seitenzahlen
            if ($this->currentPage - $center > 0) {
                $rangeStart = $this->currentPage - $center;
            }
            // Ende der anzuzeigenden Seitenzahlen
            if ($rangeStart + $this->pagesToDisplay - 1 <= $this->totalPages) {
                $rangeEnd = $rangeStart + $this->pagesToDisplay - 1;
            }

            // Anzuzeigende Seiten immer auf dem Wert von $this->pagesToDisplay halten
            if ($rangeEnd - $rangeStart < $this->pagesToDisplay && $rangeEnd - $this->pagesToDisplay > 0) {
                $rangeStart = $rangeEnd - $this->pagesToDisplay + 1;
            }
        }

        return array(
            'start' => $rangeStart,
            'end' => $rangeEnd
        );
    }

    /**
     * @param $link
     * @param $range
     */
    private function showFirstPageLink($link, $range)
    {
        if ($this->totalPages > $this->showFirstLast && $range['start'] > 1) {
            $this->pagination[] = $this->buildPageNumber(
                false,
                '&laquo;',
                $link . $this->urlFragment,
                $this->lang->t('system', 'first_page')
            );
        }
    }

    /**
     * @param        $selected
     * @param        $pageNumber
     * @param        $uri
     * @param string $title
     *
     * @return array
     */
    private function buildPageNumber($selected, $pageNumber, $uri, $title = '')
    {
        return array(
            'selected' => (bool)$selected,
            'page' => $pageNumber,
            'uri' => $uri,
            'title' => $title
        );
    }

    /**
     * @param $link
     */
    private function showPreviousPageLink($link)
    {
        if ($this->totalPages > $this->showPreviousNext && $this->currentPage !== 1) {
            $this->pagination[] = $this->buildPageNumber(
                false,
                '&lsaquo;',
                $link . ($this->currentPage - 1 > 1 ? 'page_' . ($this->currentPage - 1) . '/' : '') . $this->urlFragment,
                $this->lang->t('system', 'previous_page')
            );
        }
    }

    /**
     * @param $link
     */
    private function showNextPageLink($link)
    {
        if ($this->totalPages > $this->showPreviousNext && $this->currentPage !== $this->totalPages) {
            $this->pagination[] = $this->buildPageNumber(
                false,
                '&rsaquo;',
                $link . 'page_' . ($this->currentPage + 1) . '/' . $this->urlFragment,
                $this->lang->t('system', 'next_page')
            );
        }
    }

    /**
     * @param $link
     * @param $range
     */
    private function showLastPageLink($link, $range)
    {
        if ($this->totalPages > $this->showFirstLast && $this->totalPages !== $range['end']) {
            $this->pagination[] = $this->buildPageNumber(
                false,
                '&raquo;',
                $link . 'page_' . $this->totalPages . '/' . $this->urlFragment,
                $this->lang->t('system', 'last_page')
            );
        }
    }
}