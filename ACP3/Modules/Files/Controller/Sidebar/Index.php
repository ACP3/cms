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
     * @var \ACP3\Modules\Files\Model
     */
    protected $filesModel;

    /**
     * @param \ACP3\Core\Context        $context
     * @param \ACP3\Core\Date           $date
     * @param \ACP3\Modules\Files\Model $filesModel
     */
    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Files\Model $filesModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->filesModel = $filesModel;
    }

    /**
     * @param int    $categoryId
     * @param string $template
     */
    public function actionIndex($categoryId = 0, $template = '')
    {
        $settings = $this->config->getSettings('files');

        if (!empty($categoryId)) {
            $categories = $this->filesModel->getAllByCategoryId((int) $categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $categories = $this->filesModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }

        $this->view->assign('sidebar_files', $categories);

        $this->setTemplate($template !== '' ? $template : 'Files/Sidebar/index.index.tpl');
    }
}
