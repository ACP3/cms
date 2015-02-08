<?php

namespace ACP3\Modules\Gallery\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\Gallery\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\Gallery\Model
     */
    protected $galleryModel;

    /**
     * @param \ACP3\Core\Context          $context
     * @param \ACP3\Core\Date             $date
     * @param \ACP3\Modules\Gallery\Model $galleryModel
     */
    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Gallery\Model $galleryModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryModel = $galleryModel;
    }

    public function actionIndex()
    {
        $settings = $this->config->getSettings('gallery');

        $this->view->assign('sidebar_galleries', $this->galleryModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']));

        $this->setTemplate('Gallery/Sidebar/index.index.tpl');
    }
}
