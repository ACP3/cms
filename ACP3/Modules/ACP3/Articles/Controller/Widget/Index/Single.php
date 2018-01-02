<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Single
 * @package ACP3\Modules\ACP3\Articles\Controller\Widget\Index
 */
class Single extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache\ArticleCacheStorage
     */
    protected $articlesCache;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository
     */
    protected $articleRepository;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache\ArticleCacheStorage $articlesCache
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Articles\Model\Repository\ArticlesRepository $articleRepository,
        Articles\Cache\ArticleCacheStorage $articlesCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param int $id
     * @return array
     */
    public function execute($id)
    {
        if ($this->articleRepository->resultExists((int)$id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable();

            return [
                'sidebar_article' => $this->articlesCache->getCache($id)
            ];
        }
    }
}
