<?php
namespace ACP3\Core;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

/**
 * Class Pagination
 * @package ACP3\Core
 */
class Pagination
{
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
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
    protected $totalPages = 1;
    /**
     * @var int
     */
    protected $currentPage = 1;
    /**
     * @var array
     */
    private $pagination = [];

    /**
     * Pagination constructor.
     *
     * @param \ACP3\Core\User                  $user
     * @param \ACP3\Core\Breadcrumb\Title      $title
     * @param \ACP3\Core\I18n\Translator       $translator
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\RouterInterface       $router
     */
    public function __construct(
        User $user,
        Title $title,
        Translator $translator,
        RequestInterface $request,
        RouterInterface $router
    ) {
        $this->user = $user;
        $this->title = $title;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;

        $this->resultsPerPage = $user->getEntriesPerPage();
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
     * @return array
     */
    public function render()
    {
        if ($this->totalResults > $this->resultsPerPage) {
            $link = $this->router->route(($this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '') . $this->request->getUriWithoutPages());
            $this->currentPage = (int)$this->request->getParameters()->get('page', 1);
            $this->totalPages = (int)ceil($this->totalResults / $this->resultsPerPage);

            $this->setMetaStatements();
            $range = $this->calculateRange();

            $this->showFirstPageLink($link, $range);
            $this->showPreviousPageLink($link);

            for ($i = (int)$range['start']; $i <= $range['end']; ++$i) {
                $this->pagination[] = $this->buildPageNumber(
                    $i,
                    $link . ($i > 1 ? 'page_' . $i . '/' : '') . $this->urlFragment,
                    '',
                    $this->currentPage === $i
                );
            }

            $this->showNextPageLink($link);
            $this->showLastPageLink($link, $range);
        }

        return $this->pagination;
    }

    protected function setMetaStatements()
    {
        if ($this->currentPage > 1) {
            $postfix = $this->translator->t('system', 'page_x', ['%page%' => $this->currentPage]);
            $this->title->setPageTitlePostfix($postfix);
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
                '&laquo;',
                $link . $this->urlFragment,
                $this->translator->t('system', 'first_page')
            );
        }
    }

    /**
     * @param int    $pageNumber
     * @param string $uri
     * @param string $title
     * @param bool   $selected
     *
     * @return array
     */
    private function buildPageNumber($pageNumber, $uri, $title = '', $selected = false)
    {
        return [
            'page' => $pageNumber,
            'uri' => $uri,
            'title' => $title,
            'selected' => (bool)$selected
        ];
    }

    /**
     * @param string $link
     */
    private function showPreviousPageLink($link)
    {
        if ($this->totalPages > $this->showPreviousNext && $this->currentPage !== 1) {
            $this->pagination[] = $this->buildPageNumber(
                '&lsaquo;',
                $link . ($this->currentPage - 1 > 1 ? 'page_' . ($this->currentPage - 1) . '/' : '') . $this->urlFragment,
                $this->translator->t('system', 'previous_page')
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
                '&rsaquo;',
                $link . 'page_' . ($this->currentPage + 1) . '/' . $this->urlFragment,
                $this->translator->t('system', 'next_page')
            );
        }
    }

    /**
     * @param string $link
     * @param array  $range
     */
    private function showLastPageLink($link, array $range)
    {
        if ($this->totalPages > $this->showFirstLast && $this->totalPages !== $range['end']) {
            $this->pagination[] = $this->buildPageNumber(
                '&raquo;',
                $link . 'page_' . $this->totalPages . '/' . $this->urlFragment,
                $this->translator->t('system', 'last_page')
            );
        }
    }
}
