<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Repository;

use ACP3\Modules\ACP3\Gallery\Repository\PictureRepository;

class GalleryPictureRepository extends \ACP3\Core\Repository\AbstractRepository
{
    public const TABLE_NAME = PictureRepository::TABLE_NAME;

    /**
     * @return int[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPictureIdsByGalleryId(int $galleryId): array
    {
        $galleryPictures = $this->db->fetchAll(
            "SELECT id FROM {$this->getTableName()} where gallery_id = ?",
            [$galleryId]
        );

        return array_map(static fn (array $galleryPicture) => (int) $galleryPicture['id'], $galleryPictures);
    }
}
