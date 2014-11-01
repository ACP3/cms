<?php

namespace ACP3\Modules\News\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\News;

/**
 * Class Index
 * @package ACP3\Modules\News\Controller\Sidebar
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
     * @var News\Model
     */
    protected $newsModel;
    /**
     * @var Core\Config
     */
    protected $newsConfig;

    public function __construct(
        Core\Context $context,
        Core\Date $date,
        News\Model $newsModel,
        Core\Config $newsConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->newsModel = $newsModel;
        $this->newsConfig = $newsConfig;
    }

    public function actionIndex()
    {
        $settings = $this->newsConfig->getSettings();

        $this->view->assign('sidebar_news', $this->newsModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']));
        $this->view->assign('dateformat', $settings['dateformat']);

        $this->setLayout('News/Sidebar/index.index.tpl');
    }

}
