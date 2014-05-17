<?php

namespace ACP3\Modules\Files\Controller\Files\Sidebar;

use ACP3\Core;
use ACP3\Modules\Files;


/**
 * Description of FilesFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    /**
     *
     * @var Files\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Files\Model($this->db, $this->lang, $this->uri);
    }

   public function actionIndex()
    {
        $settings = Core\Config::getSettings('files');

        $files = $this->model->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_files = count($files);

        if ($c_files > 0) {
            for ($i = 0; $i < $c_files; ++$i) {
                $files[$i]['start'] = $this->date->format($files[$i]['start']);
                $files[$i]['title_short'] = Core\Functions::shortenEntry($files[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_files', $files);
        }

        $this->view->displayTemplate('files/sidebar.tpl');
    }

}