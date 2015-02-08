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
     * @param \ACP3\Core\Context $context
     */
    public function __construct(Core\Context $context)
    {
        parent::__construct($context);
    }

    public function actionIndex()
    {
        $this->view->assign('sidebar_contact', $this->config->getSettings('contact'));

        $this->setTemplate('Contact/Sidebar/index.index.tpl');
    }
}
