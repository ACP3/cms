<?php

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery\Model\PictureRepository;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

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
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * Helpers constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath             $appPath
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases         $aliases
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements       $metaStatements
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager      $uriAliasManager
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Aliases $aliases,
        PictureRepository $pictureRepository,
        MetaStatements $metaStatements,
        UriAliasManager $uriAliasManager
    ) {
        $this->appPath = $appPath;
        $this->aliases = $aliases;
        $this->pictureRepository = $pictureRepository;
        $this->metaStatements = $metaStatements;
        $this->uriAliasManager = $uriAliasManager;
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
        $seoKeywords = $this->metaStatements->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = $this->metaStatements->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        return $this->uriAliasManager->insertUriAlias(
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
        $cPictures = count($pictures);

        $alias = $this->aliases->getUriAlias(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId), true);
        if (!empty($alias)) {
            $alias .= '/img';
        }
        $seoKeywords = $this->metaStatements->getKeywords(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));
        $seoDescription = $this->metaStatements->getDescription(sprintf(self::URL_KEY_PATTERN_GALLERY, $galleryId));

        for ($i = 0; $i < $cPictures; ++$i) {
            $this->uriAliasManager->insertUriAlias(
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
        $cPictures = count($pictures);

        for ($i = 0; $i < $cPictures; ++$i) {
            $this->uriAliasManager->deleteUriAlias(sprintf(self::URL_KEY_PATTERN_PICTURE, $pictures[$i]['id']));
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
