<?php

namespace ACP3\Modules\Contact\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Class Index
 * @package ACP3\Modules\Contact\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $contactConfig;

    /**
     * @param Core\Context $context
     * @param Core\Config $contactConfig
     */
    public function __construct(
        Core\Context $context,
        Core\Config $contactConfig)
    {
        parent::__construct($context);

        $this->contactConfig = $contactConfig;
    }

    public function actionIndex()
    {
        $this->view->assign('sidebar_contact', $this->contactConfig->getSettings());

        $this->setTemplate('Contact/Sidebar/index.index.tpl');
    }

}