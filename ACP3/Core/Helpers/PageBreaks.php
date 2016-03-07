<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class PageBreaks
 * @package ACP3\Core\Helpers
 */
class PageBreaks
{
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\Helpers\TableOfContents
     */
    protected $tableOfContents;

    /**
     * PageBreaks constructor.
     *
     * @param \ACP3\Core\SEO                     $seo
     * @param \ACP3\Core\Http\RequestInterface   $request
     * @param \ACP3\Core\RouterInterface         $router
     * @param \ACP3\Core\Helpers\TableOfContents $tableOfContents
     */
    public function __construct(
        Core\SEO $seo,
        Core\Http\RequestInterface $request,
        Core\RouterInterface $router,
        TableOfContents $tableOfContents
    ) {
        $this->tableOfContents = $tableOfContents;
        $this->seo = $seo;
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * Parst einen Text und zerlegt diesen bei Bedarf mehrere Seiten
     *
     * @param string $text
     * @param string $baseUrlPath
     *
     * @return string|array
     */
    public function splitTextIntoPages($text, $baseUrlPath)
    {
        // Return early, if there are no page breaks
        if (strpos($text, 'class="page-break"') === false) {
            return $text;
        }

        $pages = preg_split($this->getSplitPagesRegex(), $text, -1, PREG_SPLIT_NO_EMPTY);
        $cPages = count($pages);

        // Return early, if an page breaks has been found but no content follows after it
        if ($cPages === 1) {
            return $text;
        }

        $matches = [];
        preg_match_all($this->getSplitPagesRegex(), $text, $matches);

        $currentPage = ((int)$this->request->getParameters()->get('page', 1) <= $cPages) ? (int)$this->request->getParameters()->get('page', 1) : 1;
        $nextPage = !empty($pages[$currentPage]) ? $this->router->route($baseUrlPath) . 'page_' . ($currentPage + 1) . '/' : '';
        $previousPage = $currentPage > 1 ? $this->router->route($baseUrlPath) . ($currentPage - 1 > 1 ? 'page_' . ($currentPage - 1) . '/' : '') : '';

        $this->seo->setNextPage($nextPage);
        $this->seo->setPreviousPage($previousPage);

        return [
            'toc' => $this->tableOfContents->generateTOC($matches[0], $baseUrlPath),
            'text' => $pages[$currentPage - 1],
            'next' => $nextPage,
            'previous' => $previousPage,
        ];
    }

    /**
     * @return string
     */
    protected function getSplitPagesRegex()
    {
        return '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';
    }
}
