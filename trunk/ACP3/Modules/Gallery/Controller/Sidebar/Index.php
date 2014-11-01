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
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Gallery\Model
     */
    protected $galleryModel;
    /**
     * @var Core\Config
     */
    protected $galleryConfig;

    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Gallery\Model $galleryModel,
        Core\Config $galleryConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryModel = $galleryModel;
        $this->galleryConfig = $galleryConfig;
    }

    public function actionIndex()
    {
        $settings = $this->galleryConfig->getSettings();

        $this->view->assign('sidebar_galleries', $this->galleryModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']));

        $this->setLayout('Gallery/Sidebar/index.index.tpl');
    }

}