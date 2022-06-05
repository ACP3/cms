<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Modules\ACP3\Articles\Repository\ArticleRepository;

class ArticleFullViewProvider
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $articleId): array
    {
        return [
            'sidebar_article' => $this->articleRepository->getOneById($articleId),
        ];
    }
}
