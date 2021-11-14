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
use ACP3\Modules\ACP3\Articles\Repository\ArticleRepository;

class ArticlePaginatedViewProvider
{
    /**
     * @var string|null
     */
    private $layout;

    public function __construct(private ArticleRepository $articleRepository, private PageBreaks $pageBreaksHelper, private RequestInterface $request, private Steps $breadcrumb, private Title $title, private View $view)
    {
    }

    public function __invoke(int $articleId): array
    {
        $article = $this->articleRepository->getOneById($articleId);

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
