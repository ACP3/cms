<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Modules\ACP3\Gallery\Cache;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleryPictureListWidgetViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    private $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;

    public function __construct(
        Cache $galleryCache,
        GalleryRepository $galleryRepository
    ) {
        $this->galleryCache = $galleryCache;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $galleryId): array
    {
        return [
            'gallery' => $this->galleryRepository->getOneById($galleryId),
            'pictures' => $this->galleryCache->getCache($galleryId),
        ];
    }
}
