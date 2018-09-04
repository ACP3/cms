<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Image extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    /**
     * Image constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator          $thumbnailGenerator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator
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
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action = '')
    {
        \set_time_limit(20);
        $picture = $this->pictureRepository->getFileById($id);
        $action = $action === 'thumb' ? 'thumb' : '';

        /** @var Core\Picture $image */
        $image = $this->get('core.image');

        try {
            $this->thumbnailGenerator->generateThumbnail($image, $action, $picture);

            return new RedirectResponse($image->getFileWeb());
        } catch (Core\Picture\Exception\PictureGenerateException $e) {
            throw new Core\Controller\Exception\ResultNotExistsException('', 0, $e);
        }
    }
}
