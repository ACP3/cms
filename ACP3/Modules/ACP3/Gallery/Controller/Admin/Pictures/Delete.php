<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helpers
     */
    protected $galleryHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null
     */
    private $socialSharingManager;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                            $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                              $galleryCache
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager|null            $uriAliasManager
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null    $socialSharingManager
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache,
        ?UriAliasManager $uriAliasManager,
        ?SocialSharingManager $socialSharingManager
    ) {
        parent::__construct($context);

        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
        $this->uriAliasManager = $uriAliasManager;
        $this->socialSharingManager = $socialSharingManager;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                $bool = false;
                foreach ($items as $item) {
                    if (!empty($item) && $this->pictureRepository->pictureExists($item) === true) {
                        $picture = $this->pictureRepository->getOneById($item);
                        $this->pictureRepository->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                        $this->galleryHelpers->removePicture($picture['file']);

                        $bool = $this->pictureRepository->delete($item);

                        $uri = \sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item);
                        if ($this->uriAliasManager) {
                            $this->uriAliasManager->deleteUriAlias($uri);
                        }
                        if ($this->socialSharingManager) {
                            $this->socialSharingManager->deleteSharingInfo($uri);
                        }

                        $this->galleryCache->saveCache($picture['gallery_id']);
                    }
                }

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool;
            },
            'acp/gallery/pictures/delete/id_' . $id,
            'acp/gallery/pictures/index/id_' . $id
        );
    }
}
