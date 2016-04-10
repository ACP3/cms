<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index
 */
class Details extends Core\Controller\FrontendAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Gallery\Model\PictureRepository $pictureRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function setMetaStatements(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->settings = $this->config->getSettings('gallery');
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        if ($this->pictureRepository->pictureExists($id, $this->date->getCurrentDateTime()) === true) {
            $picture = $this->pictureRepository->getPictureById($id);

            $this->breadcrumb
                ->append($this->translator->t('gallery', 'gallery'), 'gallery')
                ->append($picture['title'], 'gallery/index/pics/id_' . $picture['gallery_id'])
                ->append($this->translator->t('gallery', 'details'));

            $this->title
                ->setPageTitlePrefix($picture['title'])
                ->setPageTitlePostfix($this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']]));

            $picture = $this->calculatePictureDimensions($picture);

            $previousPicture = $this->pictureRepository->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($previousPicture)) {
                $this->setPreviousPage((int)$previousPicture);
            }

            $nextPicture = $this->pictureRepository->getNextPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($nextPicture)) {
                $this->setNextPage((int)$nextPicture);
            }

            return [
                'picture' => $picture,
                'picture_next' => $nextPicture,
                'picture_previous' => $previousPicture,
                'comments_allowed' => $this->isCommentsAllowed($picture)
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $picture
     *
     * @return array
     */
    protected function calculatePictureDimensions(array $picture)
    {
        $picture['width'] = $this->settings['width'];
        $picture['height'] = $this->settings['height'];
        $picInfos = @getimagesize($this->appPath->getUploadsDir() . 'gallery/' . $picture['file']);
        if ($picInfos !== false) {
            if ($picInfos[0] > $this->settings['width'] || $picInfos[1] > $this->settings['height']) {
                if ($picInfos[0] > $picInfos[1]) {
                    $newWidth = $this->settings['width'];
                    $newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
                } else {
                    $newHeight = $this->settings['height'];
                    $newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
                }
            }

            $picture['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
            $picture['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
        }

        return $picture;
    }

    /**
     * @param int $nextPicture
     */
    protected function setNextPage($nextPicture)
    {
        if ($this->metaStatements instanceof MetaStatements) {
            $this->metaStatements->setNextPage(
                $this->router->route(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $nextPicture))
            );
        }
    }

    /**
     * @param int $previousPicture
     */
    protected function setPreviousPage($previousPicture)
    {
        if ($this->metaStatements instanceof MetaStatements) {
            $this->metaStatements->setPreviousPage(
                $this->router->route(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $previousPicture))
            );
        }
    }

    /**
     * @param array $picture
     *
     * @return bool
     */
    protected function isCommentsAllowed(array $picture)
    {
        return $this->settings['overlay'] == 0 && $this->settings['comments'] == 1 && $picture['comments'] == 1;
    }
}
