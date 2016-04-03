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
     * @var \ACP3\Core\Validation\ValidationRules\IntegerValidationRule
     */
    protected $integerValidationRule;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * TableOfContents constructor.
     *
     * @param \ACP3\Core\Breadcrumb                                       $breadcrumb
     * @param \ACP3\Core\I18n\Translator                                  $translator
     * @param \ACP3\Core\Http\RequestInterface                            $request
     * @param \ACP3\Core\RouterInterface                                  $router
     * @param \ACP3\Core\Validation\ValidationRules\IntegerValidationRule $integerValidationRule
     * @param \ACP3\Core\View                                             $view
     */
    public function __construct(
        Core\Breadcrumb $breadcrumb,
        Core\I18n\Translator $translator,
        Core\Http\RequestInterface $request,
        Core\RouterInterface $router,
        Core\Validation\ValidationRules\IntegerValidationRule $integerValidationRule,
        Core\View $view
    ) {
        $this->breadcrumb = $breadcrumb;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->integerValidationRule = $integerValidationRule;
        $this->view = $view;
    }

    /**
     * Generates the table of contents
     *
     * @param array   $pages
     * @param string  $baseUrlPath
     * @param boolean $titlesFromDb
     * @param boolean $customUris
     *
     * @return string
     */
    public function generateTOC(array $pages, $baseUrlPath = '', $titlesFromDb = false, $customUris = false)
    {
        if (!empty($pages)) {
            $baseUrlPath = $baseUrlPath === '' ? $this->request->getUriWithoutPages() : $baseUrlPath;
            $toc = [];
            $i = 0;
            foreach ($pages as $page) {
                $pageNumber = $i + 1;
                $toc[$i]['title'] = $this->fetchTocPageTitle($page, $pageNumber, $titlesFromDb);
                $toc[$i]['uri'] = $this->fetchTocPageUri($customUris, $page, $pageNumber, $baseUrlPath);
                $toc[$i]['selected'] = $this->isCurrentPage($customUris, $page, $pageNumber, $i);

                if ($toc[$i]['selected'] === true) {
                    $this->breadcrumb->setPageTitlePostfix($toc[$i]['title']);
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
    protected function getHtmlAttributes($string)
    {
        $matches = [];
        preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

        $return = [];
        if (!empty($matches)) {
            $cMatches = count($matches[1]);
            for ($i = 0; $i < $cMatches; ++$i) {
                $return[$matches[1][$i]] = $matches[2][$i];
            }
        }

        return $return;
    }

    /**
     * @param bool         $customUris
     * @param array|string $page
     * @param int          $pageNumber
     * @param int          $currentIndex
     *
     * @return bool
     */
    protected function isCurrentPage($customUris, $page, $pageNumber, $currentIndex)
    {
        if ($customUris === true) {
            if (is_array($page) === true && $page['uri'] === $this->router->route($this->request->getQuery())
                || $this->router->route($this->request->getQuery()) === $this->router->route($this->request->getFullPath()) && $currentIndex == 0
            ) {
                return true;
            }
        } elseif (($this->integerValidationRule->isValid($this->request->getParameters()->get('page')) === false && $currentIndex === 0)
            || $this->request->getParameters()->get('page') === $pageNumber
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array|string $page
     * @param int          $pageNumber
     * @param bool         $titlesFromDb
     *
     * @return string
     */
    protected function fetchTocPageTitle($page, $pageNumber, $titlesFromDb)
    {
        if ($titlesFromDb === false && is_array($page) === false) {
            $page = $this->getHtmlAttributes($page);
        }

        $transPageNumber = $this->translator->t('system', 'toc_page', ['%page%' => $pageNumber]);
        return !empty($page['title']) ? $page['title'] : $transPageNumber;
    }

    /**
     * @param bool         $customUris
     * @param array|string $page
     * @param int          $pageNumber
     * @param string       $requestQuery
     *
     * @return string
     */
    protected function fetchTocPageUri($customUris, $page, $pageNumber, $requestQuery)
    {
        if ($customUris === true && is_array($page) === true) {
            return $page['uri'];
        }

        return $this->router->route($requestQuery) . ($pageNumber > 1 ? 'page_' . $pageNumber . '/' : '');
    }
}
