<?php

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Gallery
 */
class Helpers
{
    const URL_KEY_PATTERN_GALLERY = 'gallery/index/pics/id_%s/';
    const URL_KEY_PATTERN_PICTURE = 'gallery/index/details/id_%s/';

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * Helpers constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(Core\Environment\ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * Löscht ein Bild aus dem Dateisystem
     *
     * @param string $file
     */
    public function removePicture($file)
    {
        $upload = new Core\Helpers\Upload($this->appPath, 'cache/images');

        $upload->removeUploadedFile('gallery_thumb_' . $file);
        $upload->removeUploadedFile('gallery_' . $file);

        $upload = new Core\Helpers\Upload($this->appPath, 'gallery');
        $upload->removeUploadedFile($file);
    }
}
