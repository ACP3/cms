<?php

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery\Model\PictureRepository;

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
     * @var \ACP3\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;

    /**
     * @param \ACP3\Core\Environment\ApplicationPath             $appPath
     * @param \ACP3\Core\Router\Aliases                          $aliases
     * @param \ACP3\Core\SEO                                     $seo
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Core\Router\Aliases $aliases,
        Core\SEO $seo,
        PictureRepository $pictureRepository
    )
    {
        $this->appPath = $appPath;
        $this->aliases = $aliases;
        $this->seo = $seo;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
     *
     * @param integer $pictureId
     *
     * @return boolean
     */
    public function generatePictureAlias($pictureId)
    {
        $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($pictureId);
        $alias = $this->aliases->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img-' . $pictureId;
        }
        $seoKeywords = $this->seo->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = $this->seo->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        return $this->seo->insertUriAlias(
            sprintf(self::URL_KEY_PATTERN_PICTURE, $pictureId),
            $alias,
            $seoKeywords,
            $seoDescription
        );
    }

    /**
     * Setzt alle Bild-Aliase einer Fotogalerie neu
     *
     * @param integer $galleryId
     *
     * @return boolean
     */
    public function generatePictureAliases($galleryId)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        $alias = $this->aliases->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img';
        }
        $seoKeywords = $this->seo->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = $this->seo->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        for ($i = 0; $i < $c_pictures; ++$i) {
            $this->seo->insertUriAlias(
                sprintf(self::URL_KEY_PATTERN_PICTURE, $pictures[$i]['id']),
                !empty($alias) ? $alias . '-' . $pictures[$i]['id'] : '',
                $seoKeywords,
                $seoDescription
            );
        }

        return true;
    }

    /**
     * Sorgt dafür, dass wenn eine Fotogalerie gelöscht wird,
     * auch alle Bild-Aliase gelöscht werden
     *
     * @param integer $galleryId
     *
     * @return boolean
     */
    public function deletePictureAliases($galleryId)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        for ($i = 0; $i < $c_pictures; ++$i) {
            $this->seo->deleteUriAlias(sprintf(self::URL_KEY_PATTERN_PICTURE, $pictures[$i]['id']));
        }

        return true;
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
