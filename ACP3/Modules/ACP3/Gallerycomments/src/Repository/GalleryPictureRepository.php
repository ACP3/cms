<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;

class GalleryPictureRepository extends AbstractRepository
{
    public const TABLE_NAME = PictureRepository::TABLE_NAME;

    /**
     * @return int[]
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getPictureIdsByGalleryId(int $galleryId): array
    {
        $galleryPictures = $this->db->fetchAll(
            "SELECT id FROM {$this->getTableName()} where gallery_id = ?",
            [$galleryId]
        );

        return \array_map(static function (array $galleryPicture) {
            return (int) $galleryPicture['id'];
        }, $galleryPictures);
    }
}
