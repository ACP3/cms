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
    private ?string $layout = null;

    public function __construct(private readonly ArticleRepository $articleRepository, private readonly PageBreaks $pageBreaksHelper, private readonly RequestInterface $request, private readonly Steps $breadcrumb, private readonly Title $title, private readonly View $view)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $articleId): array
    {
        $article = $this->articleRepository->getOneById($articleId);

        $this->breadcrumb->append($article['title'], $this->request->getUriWithoutPages());
        $this->title->setPageTitle($article['title']);

        if ($article['layout'] && $this->view->templateExists($article['layout'])) {
            $this->layout = $article['layout'];
        }

        return [
            'page' => [...$article, ...$this->pageBreaksHelper->splitTextIntoPages(
                $this->view->fetchStringAsTemplate($article['text']),
                $this->request->getUriWithoutPages()
            )],
        ];
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }
}
