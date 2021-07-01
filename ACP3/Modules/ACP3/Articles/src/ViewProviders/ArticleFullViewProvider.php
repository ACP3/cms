<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;

class ArticleFullViewProvider
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke(int $articleId): array
    {
        return [
            'sidebar_article' => $this->articleRepository->getOneById($articleId),
        ];
    }
}
