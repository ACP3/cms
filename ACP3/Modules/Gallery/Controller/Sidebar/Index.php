<?php

namespace ACP3\Modules\Gallery\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Description of GalleryFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{

    /**
     *
     * @var Gallery\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Gallery\Model($this->db, $this->lang, $this->uri);
    }

    public function actionIndex()
    {
        $settings = Core\Config::getSettings('gallery');

        $galleries = $this->model->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['start'] = $this->date->format($galleries[$i]['start']);
                $galleries[$i]['title_short'] = Core\Functions::shortenEntry($galleries[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_galleries', $galleries);
        }

        $this->setLayout('Gallery/Sidebar/index.index.tpl');
    }

}