<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
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
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext         $context
     * @param \ACP3\Modules\ACP3\Gallery\Cache                   $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                 $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
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
                        $pictures = $this->pictureRepository->getPicturesByGalleryId($item);
                        foreach ($pictures as $row) {
                            $this->galleryHelpers->removePicture($row['file']);
                        }

                        $this->galleryCache->getCacheDriver()->delete(Gallery\Cache::CACHE_ID . $item);

                        if ($this->uriAliasManager) {
                            $this->uriAliasManager->deleteUriAlias(
                                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $item)
                            );
                        }

                        $this->deletePictureAliases($item);

                        $bool = $this->galleryRepository->delete($item);
                    }
                }

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool !== false;
            }
        );
    }

    /**
     * @param integer $galleryId
     *
     * @return boolean
     */
    protected function deletePictureAliases($galleryId)
    {
        if ($this->uriAliasManager) {
            $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);
            $cPictures = count($pictures);

            for ($i = 0; $i < $cPictures; ++$i) {
                $this->uriAliasManager->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $pictures[$i]['id']));
            }
        }

        return true;
    }
}
