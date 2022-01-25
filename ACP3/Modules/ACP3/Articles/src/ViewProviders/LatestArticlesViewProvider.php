<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Core\Date;
use ACP3\Modules\ACP3\Articles\Repository\ArticleRepository;

class LatestArticlesViewProvider
{
    public function __construct(private ArticleRepository $articleRepository, private Date $date)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        return [
            'sidebar_articles' => $this->articleRepository->getAll($this->date->getCurrentDateTime(), 5),
        ];
    }
}
