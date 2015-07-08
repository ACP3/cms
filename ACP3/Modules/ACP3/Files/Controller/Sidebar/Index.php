<?php

namespace ACP3\Modules\ACP3\Files\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;


/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model
     */
    protected $filesModel;

    /**
     * @param \ACP3\Core\Modules\Controller\Context        $context
     * @param \ACP3\Core\Date           $date
     * @param \ACP3\Modules\ACP3\Files\Model $filesModel
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
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
            $categories = $this->filesModel->getAllByCategoryId((int)$categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $categories = $this->filesModel->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }

        $this->view->assign('sidebar_files', $categories);

        $this->setTemplate($template !== '' ? $template : 'Files/Sidebar/index.index.tpl');
    }
}
