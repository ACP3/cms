<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;

class TableOfContents
{
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\IntegerValidationRule
     */
    private $integerValidationRule;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(
        Core\Breadcrumb\Title $title,
        Core\I18n\Translator $translator,
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        Core\Validation\ValidationRules\IntegerValidationRule $integerValidationRule,
        Core\View $view
    ) {
        $this->title = $title;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->integerValidationRule = $integerValidationRule;
        $this->view = $view;
    }

    /**
     * Generates the table of contents.
     */
    public function generateTOC(array $pages, string $baseUrlPath = '', bool $titlesFromDb = false, bool $customUris = false): string
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
                    $this->title->setPageTitlePostfix($toc[$i]['title']);
                }
                ++$i;
            }
            $this->view->assign('toc', $toc);

            return $this->view->fetchTemplate('System/Partials/toc.tpl');
        }

        return '';
    }

    /**
     * Liest aus einem String alle vorhandenen HTML-Attribute ein und
     * liefert diese als assoziatives Array zurück.
     */
    private function getHtmlAttributes(string $string): array
    {
        $matches = [];
        preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

        $return = [];
        if (!empty($matches)) {
            $cMatches = \count($matches[1]);
            for ($i = 0; $i < $cMatches; ++$i) {
                $return[$matches[1][$i]] = $matches[2][$i];
            }
        }

        return $return;
    }

    /**
     * @param array|string $page
     */
    private function isCurrentPage(bool $customUris, $page, int $pageNumber, int $currentIndex): bool
    {
        if ($customUris === true) {
            if ((\is_array($page) === true && $page['uri'] === $this->router->route($this->request->getQuery()))
                || ($this->router->route($this->request->getQuery()) === $this->router->route($this->request->getFullPath()) && $currentIndex == 0)
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
     */
    private function fetchTocPageTitle($page, int $pageNumber, bool $titlesFromDb): string
    {
        if ($titlesFromDb === false && \is_array($page) === false) {
            $page = $this->getHtmlAttributes($page);
        }

        $transPageNumber = $this->translator->t('system', 'toc_page', ['%page%' => $pageNumber]);

        return !empty($page['title']) ? $page['title'] : $transPageNumber;
    }

    /**
     * @param array|string $page
     */
    private function fetchTocPageUri(bool $customUris, $page, int $pageNumber, string $requestQuery): string
    {
        if ($customUris === true && \is_array($page) === true) {
            return $page['uri'];
        }

        return $this->router->route($requestQuery) . ($pageNumber > 1 ? 'page_' . $pageNumber . '/' : '');
    }
}
