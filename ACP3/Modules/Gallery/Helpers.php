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

abstract class Helpers
{
    /**
     *
     * @var Model
     */
    protected static $model;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(Core\Registry::get('Db'), Core\Registry::get('Lang'));
        }
    }

    /**
     * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
     *
     * @param integer $pictureId
     * @return boolean
     */
    public static function generatePictureAlias($pictureId)
    {
        self::_init();

        $galleryId = self::$model->getGalleryIdFromPictureId($pictureId);
        $alias = Core\SEO::getUriAlias('gallery/pics/id_' . $galleryId, true);
        if (!empty($alias)) {
            $alias .= '/img-' . $pictureId;
        }
        $seoKeywords = Core\SEO::getKeywords('gallery/pics/id_' . $galleryId);
        $seoDescription = Core\SEO::getDescription('gallery/pics/id_' . $galleryId);

        return Core\SEO::insertUriAlias('gallery/details/id_' . $pictureId, $alias, $seoKeywords, $seoDescription);
    }

    /**
     * Setzt alle Bild-Aliase einer Fotogalerie neu
     *
     * @param integer $galleryId
     * @return boolean
     */
    public static function generatePictureAliases($galleryId)
    {
        self::_init();

        $pictures = self::$model->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        $alias = Core\SEO::getUriAlias('gallery/pics/id_' . $galleryId, true);
        if (!empty($alias)) {
            $alias .= '/img';
        }
        $seo_keywords = Core\SEO::getKeywords('gallery/pics/id_' . $galleryId);
        $seo_description = Core\SEO::getDescription('gallery/pics/id_' . $galleryId);

        for ($i = 0; $i < $c_pictures; ++$i) {
            Core\SEO::insertUriAlias('gallery/details/id_' . $pictures[$i]['id'], !empty($alias) ? $alias . '-' . $pictures[$i]['id'] : '', $seo_keywords, $seo_description);
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
    public static function deletePictureAliases($galleryId)
    {
        self::_init();

        $pictures = self::$model->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        for ($i = 0; $i < $c_pictures; ++$i) {
            Core\SEO::deleteUriAlias('gallery/details/id_' . $pictures[$i]['id']);
        }

        return true;
    }

    /**
     * Löscht ein Bild aus dem Dateisystem
     *
     * @param string $file
     */
    public static function removePicture($file)
    {
        Core\Functions::removeUploadedFile('cache/images', 'gallery_thumb_' . $file);
        Core\Functions::removeUploadedFile('cache/images', 'gallery_' . $file);
        Core\Functions::removeUploadedFile('gallery', $file);
    }

}