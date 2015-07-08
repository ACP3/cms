<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class TableOfContents
 * @package ACP3\Core\Helpers
 */
class TableOfContents
{
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
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param \ACP3\Core\Breadcrumb            $breadcrumb
     * @param \ACP3\Core\Lang                  $lang
     * @param \ACP3\Core\SEO                   $seo
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Router                $router
     * @param \ACP3\Core\Validator\Rules\Misc  $validate
     * @param \ACP3\Core\View                  $view
     */
    public function __construct(
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\SEO $seo,
        Core\Http\RequestInterface $request,
        Core\Router $router,
        Core\Validator\Rules\Misc $validate,
        Core\View $view
    )
    {
        $this->breadcrumb = $breadcrumb;
        $this->lang = $lang;
        $this->seo = $seo;
        $this->request = $request;
        $this->router = $router;
        $this->validate = $validate;
        $this->view = $view;
    }

    /**
     * Parst einen Text und zerlegt diesen bei Bedarf mehrere Seiten
     *
     * @param string $text
     *    Der zu parsende Text
     * @param string $path
     *    Der ACP3-interne URI-Pfad, um die Links zu generieren
     *
     * @return string|array
     */
    public function splitTextIntoPages($text, $path)
    {
        // Return early, if there are no page breaks
        if (strpos($text, 'class="page-break"') === false) {
            return $text;
        }

        $pages = preg_split($this->getSplitPagesRegex(), $text, -1, PREG_SPLIT_NO_EMPTY);
        $c_pages = count($pages);

        // Return early, if an page breaks has been found but no content follows after it
        if ($c_pages === 1) {
            return $text;
        }

        $matches = [];
        preg_match_all($this->getSplitPagesRegex(), $text, $matches);

        $currentPage = ((int)$this->request->getParameters()->get('page', 1) <= $c_pages) ? (int)$this->request->getParameters()->get('page', 1) : 1;
        $nextPage = !empty($pages[$currentPage]) ? $this->router->route($path) . 'page_' . ($currentPage + 1) . '/' : '';
        $previousPage = $currentPage > 1 ? $this->router->route($path) . ($currentPage - 1 > 1 ? 'page_' . ($currentPage - 1) . '/' : '') : '';

        if (!empty($nextPage)) {
            $this->seo->setNextPage($nextPage);
        }
        if (!empty($previousPage)) {
            $this->seo->setPreviousPage($previousPage);
        }

        $page = [
            'toc' => $this->generateTOC($matches[0], $path),
            'text' => $pages[$currentPage - 1],
            'next' => $nextPage,
            'previous' => $previousPage,
        ];

        return $page;
    }

    /**
     * @return string
     */
    protected function getSplitPagesRegex()
    {
        return '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';
    }

    /**
     * Generates the table of contents
     *
     * @param array   $pages
     * @param string  $requestQuery
     * @param boolean $titlesFromDb
     * @param boolean $customUris
     *
     * @return string
     */
    public function generateTOC(array $pages, $requestQuery = '', $titlesFromDb = false, $customUris = false)
    {
        if (!empty($pages)) {
            $requestQuery = $requestQuery === '' ? $this->request->getUriWithoutPages() : $requestQuery;
            $toc = [];
            $i = 0;
            foreach ($pages as $page) {
                $pageNumber = $i + 1;
                $toc[$i]['title'] = $this->fetchTocPageTitle($page, $pageNumber, $titlesFromDb);
                $toc[$i]['uri'] = $this->fetchTocPageUri($customUris, $page, $pageNumber, $requestQuery);
                $toc[$i]['selected'] = $this->isCurrentPage($customUris, $page, $pageNumber, $i);

                if ($toc[$i]['selected'] === true) {
                    $this->breadcrumb->setTitlePostfix($toc[$i]['title']);
                }
                ++$i;
            }
            $this->view->assign('toc', $toc);
            return $this->view->fetchTemplate('system/toc.tpl');
        }
        return '';
    }

    /**
     * Liest aus einem String alle vorhandenen HTML-Attribute ein und
     * liefert diese als assoziatives Array zurück
     *
     * @param string $string
     *
     * @return array
     */
    protected function _getHtmlAttributes($string)
    {
        $matches = [];
        preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

        $return = [];
        if (!empty($matches)) {
            $c_matches = count($matches[1]);
            for ($i = 0; $i < $c_matches; ++$i) {
                $return[$matches[1][$i]] = $matches[2][$i];
            }
        }

        return $return;
    }

    /**
     * @param bool  $customUris
     * @param array $page
     * @param int   $pageNumber
     * @param int   $currentIndex
     *
     * @return bool
     */
    protected function isCurrentPage($customUris, array $page, $pageNumber, $currentIndex)
    {
        if ($customUris === true) {
            if ($page['uri'] === $this->router->route($this->request->getQuery()) ||
                $this->router->route($this->request->getQuery()) === $this->router->route($this->request->getModule() . '/' . $this->request->getController() . '/' . $this->request->getControllerAction()) && $currentIndex == 0
            ) {
                return true;
            }
        } else {
            if (($this->validate->isNumber($this->request->getParameters()->get('page')) === false && $currentIndex === 0) || $this->request->getParameters()->get('page') === $pageNumber) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $page
     * @param int   $pageNumber
     * @param bool  $titlesFromDb
     *
     * @return string
     */
    protected function fetchTocPageTitle(array $page, $pageNumber, $titlesFromDb)
    {
        if ($titlesFromDb === false) {
            $attributes = $this->_getHtmlAttributes($page);
            return !empty($attributes['title']) ? $attributes['title'] : sprintf($this->lang->t('system', 'toc_page'), $pageNumber);
        }

        return !empty($page['title']) ? $page['title'] : sprintf($this->lang->t('system', 'toc_page'), $pageNumber);
    }

    /**
     * @param bool   $customUris
     * @param array  $page
     * @param int    $pageNumber
     * @param string $requestQuery
     *
     * @return string
     */
    protected function fetchTocPageUri($customUris, array $page, $pageNumber, $requestQuery)
    {
        if ($customUris === true) {
            return $page['uri'];
        }

        return $this->router->route($requestQuery) . ($pageNumber > 1 ? 'page_' . $pageNumber . '/' : '');
    }
}
