<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures
 */
class Delete extends Core\Controller\AbstractAdminAction
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
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                 $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                   $galleryCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache
    ) {
        parent::__construct($context);

        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    if (!empty($item) && $this->pictureRepository->pictureExists($item) === true) {
                        $picture = $this->pictureRepository->getPictureById($item);
                        $this->pictureRepository->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                        $this->galleryHelpers->removePicture($picture['file']);

                        $bool = $this->pictureRepository->delete($item);
                        
                        if ($this->uriAliasManager) {
                            $this->uriAliasManager->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item));
                        }

                        $this->galleryCache->saveCache($picture['gallery_id']);
                    }
                }

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool;
            },
            'acp/gallery/pictures/delete/id_' . $id,
            'acp/gallery/index/edit/id_' . $id
        );
    }
}
