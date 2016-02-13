<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Order
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures
 */
class
Order extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\Sort
     */
    protected $sortHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;

    /**
     * Order constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext         $context
     * @param \ACP3\Core\Helpers\Sort                            $sortHelper
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                   $galleryCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\Sort $sortHelper,
        Gallery\Model\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache
    )
    {
        parent::__construct($context);

        $this->sortHelper = $sortHelper;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionOrder($id, $action)
    {
        if (($action === 'up' || $action === 'down') && $this->pictureRepository->pictureExists($id) === true) {
            if ($action === 'up') {
                $this->sortHelper->up(Gallery\Model\PictureRepository::TABLE_NAME, 'id', 'pic', $id, 'gallery_id');
            } else {
                $this->sortHelper->down(Gallery\Model\PictureRepository::TABLE_NAME, 'id', 'pic', $id, 'gallery_id');
            }

            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($id);

            $this->galleryCache->saveCache($galleryId);

            return $this->redirect()->temporary('acp/gallery/index/edit/id_' . $galleryId);
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
