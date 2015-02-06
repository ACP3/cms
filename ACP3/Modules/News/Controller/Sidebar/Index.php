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
     * @param \ACP3\Core\Context       $context
     * @param \ACP3\Core\Date          $date
     * @param \ACP3\Modules\News\Model $newsModel
     */
    public function __construct(
        Core\Context $context,
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
            $news = $this->newsModel->getAllByCategoryId((int) $categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $news = $this->newsModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }
        $this->view->assign('sidebar_news', $news);
        $this->view->assign('dateformat', $settings['dateformat']);

        $this->setTemplate($template !== '' ? $template : 'News/Sidebar/index.index.tpl');
    }
}
