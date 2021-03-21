<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core\Helpers\Upload;

class Helpers
{
    public const URL_KEY_PATTERN_GALLERY = 'gallery/index/pics/id_%s/';
    public const URL_KEY_PATTERN_PICTURE = 'gallery/index/details/id_%s/';

    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $galleryUploadHelper;

    public function __construct(
        Upload $galleryUploadHelper
    ) {
        $this->galleryUploadHelper = $galleryUploadHelper;
    }

    /**
     * Löscht ein Bild aus dem Dateisystem.
     */
    public function removePicture(string $file): void
    {
        $this->galleryUploadHelper->removeUploadedFile($file);
    }
}
