<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;

class Delete extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var Gallery\Model\GalleryModel
     */
    protected $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null
     */
    private $socialSharingManager;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Modules\ACP3\Gallery\Cache                              $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                            $galleryHelpers
     * @param Gallery\Model\GalleryModel                                    $galleryModel
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager|null            $uriAliasManager
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null    $socialSharingManager
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Cache $galleryCache,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        ?UriAliasManager $uriAliasManager,
        ?SocialSharingManager $socialSharingManager
    ) {
        parent::__construct($context);

        $this->galleryCache = $galleryCache;
        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
        $this->galleryModel = $galleryModel;
        $this->uriAliasManager = $uriAliasManager;
        $this->socialSharingManager = $socialSharingManager;
    }

    /**
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                foreach ($items as $item) {
                    $pictures = $this->pictureRepository->getPicturesByGalleryId($item);
                    foreach ($pictures as $row) {
                        $this->galleryHelpers->removePicture($row['file']);
                    }

                    $this->galleryCache->getCacheDriver()->delete(Gallery\Cache::CACHE_ID . $item);

                    $uri = \sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $item);
                    if ($this->uriAliasManager) {
                        $this->uriAliasManager->deleteUriAlias($uri);
                    }
                    if ($this->socialSharingManager) {
                        $this->socialSharingManager->deleteSharingInfo($uri);
                    }

                    $this->deletePictureAliases($item);
                }

                return $this->galleryModel->delete($items);
            }
        );
    }

    /**
     * @param int $galleryId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function deletePictureAliases(int $galleryId)
    {
        foreach ($this->pictureRepository->getPicturesByGalleryId($galleryId) as $picture) {
            $uri = \sprintf(
                Gallery\Helpers::URL_KEY_PATTERN_PICTURE,
                $picture['id']
            );

            if ($this->uriAliasManager) {
                $this->uriAliasManager->deleteUriAlias($uri);
            }
            if ($this->socialSharingManager) {
                $this->socialSharingManager->deleteSharingInfo($uri);
            }
        }

        return true;
    }
}
