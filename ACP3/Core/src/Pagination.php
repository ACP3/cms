<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Core\Router\RouterInterface;

class Pagination
{
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
     * @var \ACP3\Core\Router\RouterInterface
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
    private $showPreviousNext = 1;
    /**
     * @var int
     */
    private $pagesToDisplay = 3;
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

    public function __construct(
        Title $title,
        Translator $translator,
        RequestInterface $request,
        RouterInterface $router
    ) {
        $this->title = $title;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @param int $results
     *
     * @return $this
     */
    public function setResultsPerPage($results)
    {
        $this->resultsPerPage = (int) $results;

        return $this;
    }

    /**
     * @param int $results
     *
     * @return $this
     */
    public function setTotalResults($results)
    {
        $this->totalResults = (int) $results;

        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return $this
     */
    public function setUrlFragment($fragment)
    {
        $this->urlFragment = $fragment;

        return $this;
    }

    /**
     * @param int $pagesToDisplay
     *
     * @return $this
     */
    public function setPagesToDisplay($pagesToDisplay)
    {
        $this->pagesToDisplay = (int) $pagesToDisplay;

        return $this;
    }

    private function getPagesToDisplay(): int
    {
        $pagesToDisplay = $this->pagesToDisplay;

        $map = [
            $this->canShowNextPageLink(),
            $this->canShowPreviousPageLink(),
        ];

        foreach ($map as $result) {
            if (!$result) {
                ++$pagesToDisplay;
            }
        }

        return $pagesToDisplay;
    }

    /**
     * @param int $showFirstLast
     *
     * @return $this
     */
    public function setShowFirstLast($showFirstLast)
    {
        $this->showFirstLast = (int) $showFirstLast;

        return $this;
    }

    /**
     * @param int $showPreviousNext
     *
     * @return $this
     */
    public function setShowPreviousNext($showPreviousNext)
    {
        $this->showPreviousNext = (int) $showPreviousNext;

        return $this;
    }

    /**
     * @return int
     */
    public function getResultsStartOffset()
    {
        return (int) $this->request->getParameters()->get('page') >= 1
            ? ($this->request->getParameters()->get('page') - 1) * $this->resultsPerPage
            : 0;
    }

    /**
     * @throws \ACP3\Core\Pagination\Exception\InvalidPageException
     */
    public function render(): array
    {
        if ($this->getResultsStartOffset() > $this->totalResults) {
            throw new InvalidPageException(sprintf('Could not find any entries for page %d', $this->currentPage));
        }

        if ($this->totalResults > $this->resultsPerPage) {
            $areaPrefix = $this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '';
            $link = $areaPrefix . $this->request->getUriWithoutPages();

            $this->currentPage = (int) $this->request->getParameters()->get('page', 1);
            $this->totalPages = (int) ceil($this->totalResults / $this->resultsPerPage);

            $this->setMetaStatements();
            [$rangeStart, $rangeEnd] = $this->calculateRange();

            $this->addFirstPageLink($link, $rangeStart);
            $this->addPreviousPageLink($link);

            for ($pageNumber = $rangeStart; $pageNumber <= $rangeEnd; ++$pageNumber) {
                $this->addPageNumber(
                    $pageNumber,
                    $link . ($pageNumber > 1 ? 'page_' . $pageNumber . '/' : ''),
                    '',
                    $this->currentPage === $pageNumber
                );
            }

            $this->addNextPageLink($link);
            $this->addLastPageLink($link, $rangeEnd);
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
     * @return int[]
     */
    private function calculateRange(): array
    {
        $rangeStart = 1;
        $rangeEnd = $this->totalPages;
        $pagesToDisplay = $this->getPagesToDisplay();

        if ($this->totalPages > $pagesToDisplay) {
            $center = floor($pagesToDisplay / 2);
            $rangeStart = max(1, $this->currentPage - $center);
            $rangeEnd = min($this->totalPages, $rangeStart + $pagesToDisplay - 1);

            // Anzuzeigende Seiten immer auf dem Wert von $pagesToDisplay halten
            if ($rangeEnd === $this->totalPages) {
                $rangeStart = min($rangeStart, $rangeEnd - $pagesToDisplay + 1);
            }
        }

        return [
            (int) $rangeStart,
            (int) $rangeEnd,
        ];
    }

    private function addFirstPageLink(string $link, int $rangeStart): void
    {
        if ($this->totalPages > $this->showFirstLast && $rangeStart > 1) {
            $this->addPageNumber(
                '&laquo;',
                $link,
                $this->translator->t('system', 'first_page'),
                false,
                'pagination__first-page'
            );
        }
    }

    /**
     * @param int|string $pageNumber
     *
     * @return $this
     */
    private function addPageNumber(
        $pageNumber,
        string $uri,
        string $title = '',
        bool $selected = false,
        string $selector = ''
    ) {
        $this->pagination[] = $this->buildPageNumber($pageNumber, $uri, $title, $selected, $selector);

        return $this;
    }

    /**
     * @param int|string $pageNumber
     */
    private function buildPageNumber(
        $pageNumber,
        string $uri,
        string $title = '',
        bool $selected = false,
        string $selector = ''
    ): array {
        return [
            'page' => $pageNumber,
            'uri' => $this->router->route($uri) . $this->urlFragment,
            'title' => $title,
            'selected' => $selected,
            'selector' => $selector,
        ];
    }

    private function addPreviousPageLink(string $link): void
    {
        if ($this->canShowPreviousPageLink()) {
            $this->addPageNumber(
                '&lsaquo;',
                $link . ($this->currentPage - 1 > 1 ? 'page_' . ($this->currentPage - 1) . '/' : ''),
                $this->translator->t('system', 'previous_page'),
                false,
                'pagination__previous-page'
            );
        }
    }

    private function canShowPreviousPageLink(): bool
    {
        return $this->totalPages > $this->showPreviousNext && $this->currentPage !== 1;
    }

    private function addNextPageLink(string $link): void
    {
        if ($this->canShowNextPageLink()) {
            $this->addPageNumber(
                '&rsaquo;',
                $link . 'page_' . ($this->currentPage + 1),
                $this->translator->t('system', 'next_page'),
                false,
                'pagination__next-page'
            );
        }
    }

    private function canShowNextPageLink(): bool
    {
        return $this->totalPages > $this->showPreviousNext && $this->currentPage !== $this->totalPages;
    }

    private function addLastPageLink(string $link, int $rangeEnd): void
    {
        if ($this->totalPages > $this->showFirstLast && $this->totalPages !== $rangeEnd) {
            $this->addPageNumber(
                '&raquo;',
                $link . 'page_' . $this->totalPages,
                $this->translator->t('system', 'last_page'),
                false,
                'pagination__last-page'
            );
        }
    }
}
