<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core\Helpers\Upload;

class Helpers
{
    const URL_KEY_PATTERN_GALLERY = 'gallery/index/pics/id_%s/';
    const URL_KEY_PATTERN_PICTURE = 'gallery/index/details/id_%s/';

    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $galleryUploadHelper;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $cachedImagesUploadHelper;

    /**
     * Helpers constructor.
     */
    public function __construct(
        Upload $cachedImagesUploadHelper,
        Upload $galleryUploadHelper
    ) {
        $this->galleryUploadHelper = $galleryUploadHelper;
        $this->cachedImagesUploadHelper = $cachedImagesUploadHelper;
    }

    /**
     * Löscht ein Bild aus dem Dateisystem.
     */
    public function removePicture(string $file)
    {
        $this->cachedImagesUploadHelper->removeUploadedFile('gallery_thumb_' . $file);
        $this->cachedImagesUploadHelper->removeUploadedFile('gallery_' . $file);

        $this->galleryUploadHelper->removeUploadedFile($file);
    }
}
