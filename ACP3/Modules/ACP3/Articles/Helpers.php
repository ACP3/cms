<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;

class Helpers
{
    const URL_KEY_PATTERN = 'articles/index/details/id_%s/';
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var ArticlesRepository
     */
    protected $articleRepository;

    /**
     * @param Core\Helpers\Forms $formsHelper
     * @param ArticlesRepository  $articleRepository
     */
    public function __construct(
        Core\Helpers\Forms $formsHelper,
        ArticlesRepository $articleRepository
    ) {
        $this->formsHelper = $formsHelper;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Gibt alle angelegten Artikel zurück
     *
     * @param integer $id
     *
     * @return array
     */
    public function articlesList($id = 0)
    {
        $articles = $this->articleRepository->getAll();
        $cArticles = \count($articles);

        if ($cArticles > 0) {
            for ($i = 0; $i < $cArticles; ++$i) {
                $articles[$i]['selected'] = $this->formsHelper->selectEntry('articles', $articles[$i]['id'], $id);
            }
        }

        return $articles;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function articleExists($id)
    {
        return $this->articleRepository->resultExists($id);
    }
}
