<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache\FileCacheStorage
     */
    protected $filesCache;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache\FileCacheStorage $filesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block,
        Core\Date $date,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache\FileCacheStorage $filesCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
        $this->block = $block;
    }

    /**
     * @param int $id
     * @return array
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(int $id)
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable();

            return $this->block
                ->setData($this->filesCache->getCache($id))
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
