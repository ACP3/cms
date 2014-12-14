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
     * @var Files\Model
     */
    protected $filesModel;
    /**
     * @var Core\Config
     */
    protected $filesConfig;

    /**
     * @param Core\Context $context
     * @param Core\Date $date
     * @param Files\Model $filesModel
     * @param Core\Config $filesConfig
     */
    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Files\Model $filesModel,
        Core\Config $filesConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->filesModel = $filesModel;
        $this->filesConfig = $filesConfig;
    }

    /**
     * @param int    $categoryId
     * @param string $template
     */
    public function actionIndex($categoryId = 0, $template = '')
    {
        $settings = $this->filesConfig->getSettings();

        if (!empty($categoryId)) {
            $categories = $this->filesModel->getAllByCategoryId((int) $categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $categories = $this->filesModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }

        $this->view->assign('sidebar_files', $categories);

        $this->setTemplate($template !== '' ? $template : 'Files/Sidebar/index.index.tpl');
    }
}
