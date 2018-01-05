<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache\ArticleCacheStorage
     */
    protected $articlesCache;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date $date
     * @param Core\View\Block\BlockInterface $block
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache\ArticleCacheStorage $articlesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\View\Block\BlockInterface $block,
        Articles\Model\Repository\ArticlesRepository $articleRepository,
        Articles\Cache\ArticleCacheStorage $articlesCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(int $id)
    {
        if ($this->articleRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable();

            return $this->block
                ->setData($this->articlesCache->getCache($id))
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
