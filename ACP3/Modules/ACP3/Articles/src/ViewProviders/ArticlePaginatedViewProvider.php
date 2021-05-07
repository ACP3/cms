<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\PageBreaks;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Articles\Cache;

class ArticlePaginatedViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    private $articlesCache;
    /**
     * @var \ACP3\Core\Helpers\PageBreaks
     */
    private $pageBreaksHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var string|null
     */
    private $layout;

    public function __construct(
        Cache $articlesCache,
        PageBreaks $pageBreaksHelper,
        RequestInterface $request,
        Steps $breadcrumb,
        Title $title,
        View $view
    ) {
        $this->articlesCache = $articlesCache;
        $this->pageBreaksHelper = $pageBreaksHelper;
        $this->request = $request;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->view = $view;
    }

    public function __invoke(int $articleId): array
    {
        $article = $this->articlesCache->getCache($articleId);

        $this->breadcrumb->append($article['title'], $this->request->getUriWithoutPages());
        $this->title->setPageTitle($article['title']);

        if ($this->view->templateExists($article['layout'])) {
            $this->layout = $article['layout'];
        }

        return [
            'page' => array_merge(
                $article,
                $this->pageBreaksHelper->splitTextIntoPages(
                    $this->view->fetchStringAsTemplate($article['text']),
                    $this->request->getUriWithoutPages()
                )
            ),
        ];
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }
}
