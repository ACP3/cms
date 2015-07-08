<?php

namespace ACP3\Modules\ACP3\News\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\News\Controller\Sidebar
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
     * @param \ACP3\Core\Modules\Controller\Context $context
     * @param \ACP3\Core\Date                       $date
     * @param \ACP3\Modules\ACP3\News\Model         $newsModel
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Date $date,
        News\Model $newsModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->newsModel = $newsModel;
    }

    /**
     * @param int    $categoryId
     * @param string $template
     */
    public function actionIndex($categoryId = 0, $template = '')
    {
        $settings = $this->config->getSettings('news');

        if (!empty($categoryId)) {
            $news = $this->newsModel->getAllByCategoryId((int)$categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $news = $this->newsModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }
        $this->view->assign('sidebar_news', $news);
        $this->view->assign('dateformat', $settings['dateformat']);

        $this->setTemplate($template !== '' ? $template : 'News/Sidebar/index.index.tpl');
    }

    /**
     * @param int $categoryId
     */
    public function actionLatest($categoryId = 0)
    {
        $settings = $this->config->getSettings('news');

        if (!empty($categoryId)) {
            $news = $this->newsModel->getLatestByCategoryId((int)$categoryId, $this->date->getCurrentDateTime());
        } else {
            $news = $this->newsModel->getLatest($this->date->getCurrentDateTime());
        }

        $this->view->assign('sidebar_news_latest', $news);
        $this->view->assign('dateformat', $settings['dateformat']);

        $this->setTemplate('News/Sidebar/index.latest.tpl');
    }
}
