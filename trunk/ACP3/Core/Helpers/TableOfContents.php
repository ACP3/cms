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
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Validate
     */
    protected $validate;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    public function __construct(
        Core\Breadcrumb $breadcrumb, 
        Core\Lang $lang, 
        Core\SEO $seo,
        Core\Request $request,
        Core\Router $router,
        Core\Validate $validate,
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
     * Generiert ein Inhaltsverzeichnis
     *
     * @param array $pages
     * @param string $path
     * @param boolean $titlesFromDb
     * @param boolean $customUris
     * @return string
     */
    public function generateTOC(array $pages, $path = '', $titlesFromDb = false, $customUris = false)
    {
        if (!empty($pages)) {
            $request = $this->request;
            $path = empty($path) ? $request->getUriWithoutPages() : $path;
            $toc = array();
            $i = 0;
            foreach ($pages as $page) {
                $pageNumber = $i + 1;
                if ($titlesFromDb === false) {
                    $attributes = self::_getHtmlAttributes($page);
                    $toc[$i]['title'] = !empty($attributes['title']) ? $attributes['title'] : sprintf($this->lang->t('system', 'toc_page'), $pageNumber);
                } else {
                    $toc[$i]['title'] = !empty($page['title']) ? $page['title'] : sprintf($this->lang->t('system', 'toc_page'), $pageNumber);
                }

                $toc[$i]['uri'] = $customUris === true ? $page['uri'] : $this->router->route($path) . ($pageNumber > 1 ? 'page_' . $pageNumber . '/' : '');

                $toc[$i]['selected'] = false;
                if ($customUris === true) {
                    if ($page['uri'] === $this->router->route($request->query) ||
                        $this->router->route($request->query) === $this->router->route($request->mod . '/' . $request->controller . '/' . $request->file) && $i == 0
                    ) {
                        $toc[$i]['selected'] = true;
                        $this->breadcrumb->setTitlePostfix($toc[$i]['title']);
                    }
                } else {
                    if (($this->validate->isNumber($request->page) === false && $i === 0) || $request->page === $pageNumber) {
                        $toc[$i]['selected'] = true;
                        $this->breadcrumb->setTitlePostfix($toc[$i]['title']);
                    }
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
     * @return array
     */
    protected function _getHtmlAttributes($string)
    {
        $matches = array();
        preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

        $return = array();
        if (!empty($matches)) {
            $c_matches = count($matches[1]);
            for ($i = 0; $i < $c_matches; ++$i) {
                $return[$matches[1][$i]] = $matches[2][$i];
            }
        }

        return $return;
    }

    /**
     * Parst einen Text und zerlegt diesen bei Bedarf mehrere Seiten
     *
     * @param string $text
     *    Der zu parsende Text
     * @param string $path
     *    Der ACP3-interne URI-Pfad, um die Links zu generieren
     * @return string|array
     */
    public function splitTextIntoPages($text, $path)
    {
        // Falls keine Seitenumbrüche vorhanden sein sollten, Text nicht unnötig bearbeiten
        if (strpos($text, 'class="page-break"') === false) {
            return $text;
        } else {
            $regex = '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';

            $pages = preg_split($regex, $text, -1, PREG_SPLIT_NO_EMPTY);
            $c_pages = count($pages);

            // Falls zwar Seitenumbruch gesetzt ist, aber danach
            // kein weiterer Text kommt, den unbearbeiteten Text ausgeben
            if ($c_pages == 1) {
                return $text;
            } else {
                $matches = array();
                preg_match_all($regex, $text, $matches);

                $currentPage = $this->validate->isNumber($this->request->page) === true && $this->request->page <= $c_pages ? $this->request->page : 1;
                $nextPage = !empty($pages[$currentPage]) ? $this->router->route($path) . 'page_' . ($currentPage + 1) . '/' : '';
                $previousPage = $currentPage > 1 ? $this->router->route($path) . ($currentPage - 1 > 1 ? 'page_' . ($currentPage - 1) . '/' : '') : '';

                if (!empty($nextPage)) {
                    $this->seo->setNextPage($nextPage);
                }
                if (!empty($previousPage)) {
                    $this->seo->setPreviousPage($previousPage);
                }

                $page = array(
                    'toc' => $this->generateTOC($matches[0], $path),
                    'text' => $pages[$currentPage - 1],
                    'next' => $nextPage,
                    'previous' => $previousPage,
                );

                return $page;
            }
        }
    }

} 