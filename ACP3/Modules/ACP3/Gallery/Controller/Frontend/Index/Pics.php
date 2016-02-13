<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Pics
 * @package ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index
 */
class Pics extends AbstractAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;

    /**
     * Pics constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext      $context
     * @param \ACP3\Core\Date                                    $date
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                   $galleryCache
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Gallery\Model\GalleryRepository $galleryRepository,
        Gallery\Cache $galleryCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->galleryCache = $galleryCache;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        if ($this->galleryRepository->galleryExists($id, $this->date->getCurrentDateTime()) === true) {
            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->translator->t('gallery', 'gallery'), 'gallery')
                ->append($this->galleryRepository->getGalleryTitle($id));

            return [
                'pictures' => $this->galleryCache->getCache($id),
                'overlay' => (int)$this->settings['overlay']
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
