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
        $formatter = $this->get('core.helpers.stringFormatter');
        $settings = $this->newsConfig->getSettings();

        $news = $this->newsModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_news = count($news);

        if ($c_news > 0) {
            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['start'] = $this->date->format($news[$i]['start'], $settings['dateformat']);
                $news[$i]['title_short'] = $formatter->shortenEntry($news[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_news', $news);
        }

        $this->setLayout('News/Sidebar/index.index.tpl');
    }

}
