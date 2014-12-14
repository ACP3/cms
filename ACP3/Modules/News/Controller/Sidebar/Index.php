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
     * @var News\Model
     */
    protected $newsModel;
    /**
     * @var Core\Config
     */
    protected $newsConfig;

    /**
     * @param Core\Context $context
     * @param Core\Date $date
     * @param News\Model $newsModel
     * @param Core\Config $newsConfig
     */
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

        $this->setTemplate('News/Sidebar/index.index.tpl');
    }
}
