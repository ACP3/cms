<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Gallery\Controller\Widget\Index
 */
class Index extends Core\Controller\WidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryModel;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\WidgetContext        $context
     * @param \ACP3\Core\Date                                    $date
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryModel
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Gallery\Model\GalleryRepository $galleryModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryModel = $galleryModel;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $this->setCacheResponseCacheable();

        $settings = $this->config->getSettings('gallery');

        return [
            'sidebar_galleries' => $this->galleryModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar'])
        ];
    }
}
