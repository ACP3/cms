<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\News\Cache\NewsCacheStorage
     */
    protected $newsCache;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache\NewsCacheStorage $newsCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block,
        Core\Date $date,
        News\Model\Repository\NewsRepository $newsRepository,
        News\Cache\NewsCacheStorage $newsCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        if ($this->newsRepository->resultExists($id, $this->date->getCurrentDateTime()) == 1) {
            $this->setCacheResponseCacheable();

            return $this->block
                ->setData($this->newsCache->getCache($id))
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
