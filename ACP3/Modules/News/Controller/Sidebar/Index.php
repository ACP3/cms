<?php

namespace ACP3\Modules\News\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\News;

/**
 * Class Index
 * @package ACP3\Modules\News\Controller\Sidebar
 */
class Index extends Core\Modules\Controller\Sidebar
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

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        News\Model $newsModel)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->date = $date;
        $this->db = $db;
        $this->newsModel = $newsModel;
    }

    public function actionIndex()
    {
        $formatter = $this->get('core.helpers.string.formatter');
        $config = new Core\Config($this->db, 'news');
        $settings = $config->getSettings();

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
