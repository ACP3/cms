<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Services;

use ACP3\Core\Helpers\Upload;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Model\PictureModel;
use ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GalleryPictureUpsertService
{
    public function __construct(private readonly Upload $galleryUploadHelper, private readonly PictureModel $pictureModel, private readonly PictureFormValidation $pictureFormValidation, private readonly ThumbnailGenerator $thumbnailGenerator)
    {
    }

    /**
     * @param array<string, mixed> $updatedData
     */
    public function upsert(array $updatedData, ?UploadedFile $file, ?int $galleryPictureId = null): int
    {
        $this->pictureFormValidation
            ->withFile($file, $galleryPictureId === null)
            ->validate($updatedData);

        if ($file !== null) {
            $result = $this->galleryUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
            $updatedData['file'] = $result['name'];

            if ($galleryPictureId !== null) {
                $currentData = $this->pictureModel->getOneById($galleryPictureId);
                $this->thumbnailGenerator->removePictureFromFilesystem($currentData['file']);
            }
        }

        return $this->pictureModel->save($updatedData, $galleryPictureId);
    }
}
