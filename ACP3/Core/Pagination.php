<?php
namespace ACP3\Core;

use ACP3\Core\Validator\Rules\Misc;

/**
 * Class Pagination
 * @package ACP3\Core
 */
class Pagination
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var \ACP3\Core\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
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

    /**
     * @param \ACP3\Core\Auth                 $auth
     * @param \ACP3\Core\Breadcrumb           $breadcrumb
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\SEO                  $seo
     * @param \ACP3\Core\RequestInterface     $request
     * @param \ACP3\Core\Router               $router
     * @param \ACP3\Core\View                 $view
     * @param \ACP3\Core\Validator\Rules\Misc $miscValidator
     */
    public function __construct(
        Auth $auth,
        Breadcrumb $breadcrumb,
        Lang $lang,
        SEO $seo,
        RequestInterface $request,
        Router $router,
        View $view,
        Misc $miscValidator)
    {
        $this->auth = $auth;
        $this->breadcrumb = $breadcrumb;
        $this->lang = $lang;
        $this->seo = $seo;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->miscValidator = $miscValidator;

        $this->resultsPerPage = $auth->entries;
    }

    /**
     * @param int $results
     */
    public function setResultsPerPage($results)
    {
        $this->resultsPerPage = (int)$results;
    }

    /**
     * @param int $results
     */
    public function setTotalResults($results)
    {
        $this->totalResults = (int)$results;
    }

    /**
     * @param string $fragment
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
            $link = $this->router->route(($this->request->getArea() === 'admin' ? 'acp/' : '') . $this->request->getUriWithoutPages());
            $this->currentPage = (int) $this->request->getParameters()->get('page', 1);
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
     * @param string $link
     */
    private function setMetaStatements($link)
    {
        if ($this->currentPage > 1) {
            $postfix = sprintf($this->lang->t('system', 'page_x'), $this->currentPage);
            $this->breadcrumb->setTitlePostfix($postfix);
        }

        // Vorherige und nächste Seite für Suchmaschinen und Prefetching propagieren
        if ($this->request->getArea() !== 'admin') {
            if ($this->currentPage - 1 > 0) {
                // Seitenangabe in der Seitenbeschreibung ab Seite 2 angeben
                $this->seo->setDescriptionPostfix(sprintf($this->lang->t('system', 'page_x'), $this->currentPage));
                $this->seo->setPreviousPage($link . 'page_' . ($this->currentPage - 1) . '/');
            }
            if ($this->currentPage + 1 <= $this->totalPages) {
                $this->seo->setNextPage($link . 'page_' . ($this->currentPage + 1) . '/');
            }
            if ($this->request->getParameters()->get('page', 0) === 1) {
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

        return [
            'start' => $rangeStart,
            'end' => $rangeEnd
        ];
    }

    /**
     * @param string $link
     * @param array  $range
     */
    private function showFirstPageLink($link, array $range)
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
     * @param bool   $selected
     * @param int    $pageNumber
     * @param string $uri
     * @param string $title
     *
     * @return array
     */
    private function buildPageNumber($selected, $pageNumber, $uri, $title = '')
    {
        return [
            'selected' => (bool)$selected,
            'page' => $pageNumber,
            'uri' => $uri,
            'title' => $title
        ];
    }

    /**
     * @param string $link
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
     * @param string $link
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
     * @param string $link
     * @param array $range
     */
    private function showLastPageLink($link, array $range)
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
