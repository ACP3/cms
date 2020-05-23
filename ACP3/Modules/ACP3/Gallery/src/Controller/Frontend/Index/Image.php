<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Image extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, ?string $action = null)
    {
        \set_time_limit(20);
        $picture = $this->pictureRepository->getFileById($id);
        $action = $action === 'thumb' ? 'thumb' : '';

        try {
            return new RedirectResponse($this->thumbnailGenerator->generateThumbnail($picture, $action)->getFileWeb());
        } catch (Core\Picture\Exception\PictureGenerateException $e) {
            throw new Core\Controller\Exception\ResultNotExistsException('', 0, $e);
        }
    }
}
