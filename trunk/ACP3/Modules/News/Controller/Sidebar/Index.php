<?php

namespace ACP3\Modules\News\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\News;

/**
 * Description of NewsFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{

    /**
     *
     * @var News\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new News\Model($this->db);
    }

    public function actionIndex()
    {
        $config = new Core\Config($this->db, 'news');
        $settings = $config->getSettings();

        $news = $this->model->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_news = count($news);

        if ($c_news > 0) {
            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['start'] = $this->date->format($news[$i]['start'], $settings['dateformat']);
                $news[$i]['title_short'] = Core\Functions::shortenEntry($news[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_news', $news);
        }

        $this->setLayout('News/Sidebar/index.index.tpl');
    }

}
