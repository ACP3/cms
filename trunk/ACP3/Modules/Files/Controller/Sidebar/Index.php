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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Files\Model
     */
    protected $filesModel;

    public function __construct(
        Core\Context $context,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Files\Model $filesModel)
    {
       parent::__construct($context);

        $this->date = $date;
        $this->db = $db;
        $this->filesModel = $filesModel;
    }

    public function actionIndex()
    {
        $formatter = $this->get('core.helpers.string.formatter');
        $config = new Core\Config($this->db, 'files');
        $settings = $config->getSettings();

        $files = $this->filesModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_files = count($files);

        if ($c_files > 0) {
            for ($i = 0; $i < $c_files; ++$i) {
                $files[$i]['start'] = $this->date->format($files[$i]['start']);
                $files[$i]['title_short'] = $formatter->shortenEntry($files[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_files', $files);
        }

        $this->setLayout('Files/Sidebar/index.index.tpl');
    }

}