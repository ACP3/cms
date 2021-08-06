<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Modules\ACP3\Gallery\Services\GalleryServiceInterface;

class GalleryPictureListWidgetViewProvider
{
    /**
     * @var GalleryServiceInterface
     */
    private $galleryService;

    public function __construct(
        GalleryServiceInterface $galleryService
    ) {
        $this->galleryService = $galleryService;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $galleryId): array
    {
        return [
            'gallery' => $this->galleryService->getGalleryWithPictures($galleryId),
        ];
    }
}
