<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Image extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository
     */
    protected $pictureRepository;
    /**
     * @var Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    /**
     * Image constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Gallery\Helper\ThumbnailGenerator $thumbnailGenerator
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository $pictureRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        Gallery\Model\Repository\GalleryPicturesRepository $pictureRepository
    ) {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action = '')
    {
        set_time_limit(20);
        $picture = $this->pictureRepository->getFileById($id);
        $action = $action === 'thumb' ? 'thumb' : '';

        /** @var Core\Picture $image */
        $image = $this->get('core.image');
        $this->thumbnailGenerator->generateThumbnail($image, $action, $picture);

        if ($image->process()) {
            return $image->sendResponse();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
