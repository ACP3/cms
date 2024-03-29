<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;

class PageBreaks
{
    public function __construct(private readonly Core\Http\RequestInterface $request, private readonly Core\Router\RouterInterface $router, private readonly TableOfContents $tableOfContents)
    {
    }

    /**
     * Parst einen Text und zerlegt diesen bei Bedarf mehrere Seiten.
     *
     * @return array{toc: string, text: string, next: string, previous: string}
     */
    public function splitTextIntoPages(string $text, string $baseUrlPath): array
    {
        $matches = [];
        preg_match_all($this->getSplitPagesRegex(), $text, $matches);
        $pages = preg_split($this->getSplitPagesRegex(), $text, -1, PREG_SPLIT_NO_EMPTY);

        if ($pages === false) {
            throw new \RuntimeException('An error occurred, while paginating the text.');
        }

        $currentPage = $this->getCurrentPage($pages);
        $nextPage = !empty($pages[$currentPage]) ? $this->router->route($baseUrlPath) . 'page_' . ($currentPage + 1) . '/' : '';
        $previousPage = $currentPage > 1 ? $this->router->route($baseUrlPath) . ($currentPage - 1 > 1 ? 'page_' . ($currentPage - 1) . '/' : '') : '';

        return [
            'toc' => $this->tableOfContents->generateTOC($matches[0], $baseUrlPath),
            'text' => $pages[$currentPage - 1],
            'next' => $nextPage,
            'previous' => $previousPage,
        ];
    }

    private function getSplitPagesRegex(): string
    {
        return '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';
    }

    /**
     * @param string[] $pages
     */
    private function getCurrentPage(array $pages): int
    {
        $currentPage = (int) $this->request->getParameters()->get('page', 1);

        return ($currentPage <= \count($pages)) ? $currentPage : 1;
    }
}
