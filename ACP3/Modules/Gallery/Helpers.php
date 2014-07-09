<?php

/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Gallery;

use ACP3\Core;

class Helpers
{
    const URL_KEY_PATTERN_GALLERY = 'gallery/index/pics/id_%s/';
    const URL_KEY_PATTERN_PICTURE = 'gallery/index/details/id_%s/';

    /**
     *
     * @var Model
     */
    protected $galleryModel;

    /**
     * @var Core\URI
     */
    protected $uri;

    /**
     * @var Core\SEO
     */
    protected $seo;

    public function __construct(Core\URI $uri, Core\SEO $seo, Model $galleryModel)
    {
            $this->uri = $uri;
            $this->seo = $seo;
            $this->galleryModel = $galleryModel;
    }

    /**
     * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
     *
     * @param integer $pictureId
     * @return boolean
     */
    public function generatePictureAlias($pictureId)
    {
        $galleryId = $this->galleryModel->getGalleryIdFromPictureId($pictureId);
        $alias = $this->uri->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img-' . $pictureId;
        }
        $seoKeywords = $this->seo->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = $this->seo->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        return $this->uri->insertUriAlias(
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
     * @return boolean
     */
    public function generatePictureAliases($galleryId)
    {
        $pictures = $this->galleryModel->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        $alias = $this->uri->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img';
        }
        $seoKeywords = $this->seo->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = $this->seo->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        for ($i = 0; $i < $c_pictures; ++$i) {
            $this->uri->insertUriAlias(
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
     * @return boolean
     */
    public function deletePictureAliases($galleryId)
    {
        $pictures = $this->galleryModel->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        for ($i = 0; $i < $c_pictures; ++$i) {
            $this->uri->deleteUriAlias(sprintf(self::URL_KEY_PATTERN_PICTURE, $pictures[$i]['id']));
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
        $upload = new Core\Helpers\Upload('cache/images');

        $upload->removeUploadedFile('gallery_thumb_' . $file);
        $upload->removeUploadedFile('gallery_' . $file);

        $upload = new Core\Helpers\Upload('gallery');
        $upload->removeUploadedFile($file);
    }

}