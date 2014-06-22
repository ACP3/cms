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
    const URL_KEY_PATTERN_GALLERY = 'gallery/index/pics/id_%s/';
    const URL_KEY_PATTERN_PICTURE = 'gallery/index/details/id_%s/';

    /**
     *
     * @var Model
     */
    protected static $model;

    /**
     * @var Core\URI
     */
    protected static $uri;

    /**
     * @var Core\SEO
     */
    protected static $seo;

    protected static function _init()
    {
        if (!self::$model) {
            self::$uri = Core\Registry::get('URI');
            self::$seo = Core\Registry::get('SEO');
            self::$model = new Model(Core\Registry::get('Db'));
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
        $alias = self::$uri->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img-' . $pictureId;
        }
        $seoKeywords = self::$seo->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = self::$seo->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        return self::$uri->insertUriAlias(
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
    public static function generatePictureAliases($galleryId)
    {
        self::_init();

        $pictures = self::$model->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        $alias = self::$uri->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img';
        }
        $seoKeywords = self::$seo->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = self::$seo->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        for ($i = 0; $i < $c_pictures; ++$i) {
            self::$uri->insertUriAlias(
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
    public static function deletePictureAliases($galleryId)
    {
        self::_init();

        $pictures = self::$model->getPicturesByGalleryId($galleryId);
        $c_pictures = count($pictures);

        for ($i = 0; $i < $c_pictures; ++$i) {
            self::$uri->deleteUriAlias(sprintf(self::URL_KEY_PATTERN_PICTURE, $pictures[$i]['id']));
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