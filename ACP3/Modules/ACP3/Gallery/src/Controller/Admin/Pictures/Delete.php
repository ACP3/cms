<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Gallery;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helpers
     */
    private $galleryHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    private $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var \Toflar\Psr6HttpCacheStore\Psr6Store
     */
    private $httpCacheStore;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache,
        Psr6Store $httpCacheStore
    ) {
        parent::__construct($context);

        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
        $this->actionHelper = $actionHelper;
        $this->httpCacheStore = $httpCacheStore;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(int $id, ?string $action = null)
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

                        $this->galleryCache->saveCache($picture['gallery_id']);
                    }
                }

                $this->httpCacheStore->clear();

                return $bool;
            },
            'acp/gallery/pictures/delete/id_' . $id,
            'acp/gallery/pictures/index/id_' . $id
        );
    }
}
