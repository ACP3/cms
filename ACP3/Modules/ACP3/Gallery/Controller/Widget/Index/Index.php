<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Gallery\Controller\Widget\Index
 */
class Index extends Core\Modules\Controller
{
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
     * @param \ACP3\Core\Modules\Controller\Context              $context
     * @param \ACP3\Core\Date                                    $date
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryModel
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Date $date,
        Gallery\Model\GalleryRepository $galleryModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryModel = $galleryModel;
    }

    public function execute()
    {
        $settings = $this->config->getSettings('gallery');

        $this->view->assign('sidebar_galleries', $this->galleryModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']));

        $this->setTemplate('Gallery/Widget/index.index.tpl');
    }
}
