<?php

namespace ACP3\Modules\Files\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Files;


/**
 * Class Index
 * @package ACP3\Modules\Files\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var Files\Model
     */
    protected $filesModel;
    /**
     * @var Core\Config
     */
    protected $filesConfig;

    /**
     * @param Core\Context $context
     * @param Core\Date $date
     * @param Files\Model $filesModel
     * @param Core\Config $filesConfig
     */
    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Files\Model $filesModel,
        Core\Config $filesConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->filesModel = $filesModel;
        $this->filesConfig = $filesConfig;
    }

    public function actionIndex()
    {
        $settings = $this->filesConfig->getSettings();

        $this->view->assign('sidebar_files', $this->filesModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']));

        $this->setTemplate('Files/Sidebar/index.index.tpl');
    }

}