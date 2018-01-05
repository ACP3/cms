<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository
     */
    protected $pictureRepository;
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
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository $pictureRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block,
        Core\Date $date,
        Gallery\Model\Repository\GalleryPicturesRepository $pictureRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pictureRepository = $pictureRepository;
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
        if ($this->pictureRepository->pictureExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable();

            return $this->block
                ->setData(['picture_id' => $id])
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
