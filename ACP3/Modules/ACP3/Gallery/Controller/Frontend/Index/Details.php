<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index
 */
class Details extends Core\Modules\FrontendController
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
     * @var array
     */
    protected $settings = [];

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext      $context
     * @param \ACP3\Core\Date                                    $date
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Gallery\Model\PictureRepository $pictureRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pictureRepository = $pictureRepository;
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
                ->append($this->translator->t('gallery', 'details'))
                ->setTitlePrefix($picture['title'])
                ->setTitlePostfix($this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']]));

            // Bildabmessungen berechnen
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

            // Previous picture
            $previousPicture = $this->pictureRepository->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($previousPicture)) {
                $this->seo->setPreviousPage($this->router->route('gallery/index/details/id_' . $previousPicture));
                $this->view->assign('picture_back', $previousPicture);
            }

            // Next picture
            $nextPicture = $this->pictureRepository->getNextPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($nextPicture)) {
                $this->seo->setNextPage($this->router->route('gallery/index/details/id_' . $nextPicture));
                $this->view->assign('picture_next', $nextPicture);
            }

            return [
                'picture' => $picture,
                'comments_allowed' => $this->settings['overlay'] == 0 && $this->settings['comments'] == 1 && $picture['comments'] == 1
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
