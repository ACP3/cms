<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Image
 * @package ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index
 */
class Image extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;

    /**
     * Image constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext      $context
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Gallery\Model\PictureRepository $pictureRepository)
    {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id, $action = '')
    {
        set_time_limit(20);
        $picture = $this->pictureRepository->getFileById($id);
        $action = $action === 'thumb' ? 'thumb' : '';

        /** @var Core\Picture $image */
        $image = $this->get('core.image');
        $image
            ->setEnableCache($this->config->getSettings('system')['cache_images'] == 1)
            ->setCachePrefix('gallery_' . $action)
            ->setMaxWidth($this->settings[$action . 'width'])
            ->setMaxHeight($this->settings[$action . 'height'])
            ->setFile($this->appPath->getUploadsDir() . 'gallery/' . $picture)
            ->setPreferHeight($action === 'thumb');

        if ($image->process()) {
            return $image->sendResponse();
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
