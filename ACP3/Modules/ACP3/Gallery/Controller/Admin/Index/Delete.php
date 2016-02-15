<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Delete extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helpers
     */
    protected $galleryHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext         $context
     * @param \ACP3\Modules\ACP3\Gallery\Cache                   $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                 $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Gallery\Cache $galleryCache,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\GalleryRepository $galleryRepository,
        Gallery\Model\PictureRepository $pictureRepository)
    {
        parent::__construct($context);

        $this->galleryCache = $galleryCache;
        $this->galleryHelpers = $galleryHelpers;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @param string $action
     *
     * @return mixed
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->galleryRepository->galleryExists($item) === true) {
                        // Hochgeladene Bilder löschen
                        $pictures = $this->pictureRepository->getPicturesByGalleryId($item);
                        foreach ($pictures as $row) {
                            $this->galleryHelpers->removePicture($row['file']);
                        }

                        // Galerie Cache löschen
                        $this->galleryCache->getCacheDriver()->delete(Gallery\Cache::CACHE_ID . $item);
                        $this->seo->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $item));
                        $this->galleryHelpers->deletePictureAliases($item);

                        // Fotogalerie mitsamt Bildern löschen
                        $bool = $this->galleryRepository->delete($item);
                    }
                }

                return $bool !== false;
            }
        );
    }
}
