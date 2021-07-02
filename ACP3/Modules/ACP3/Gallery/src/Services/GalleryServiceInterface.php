<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Services;

interface GalleryServiceInterface
{
    public function getGallery(int $galleryId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getGalleryPictures(int $galleryId): array;

    /**
     * @return array<string, mixed>
     */
    public function getGalleryWithPictures(int $galleryId): array;
}
