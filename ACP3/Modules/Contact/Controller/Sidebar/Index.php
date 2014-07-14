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

    public function __construct(
        Core\Context $context,
        Core\Config $contactConfig)
    {
       parent::__construct($context);

        $this->contactConfig = $contactConfig;
    }

    public function actionIndex()
    {
        $formatter = $this->get('core.helpers.string.formatter');

        $settings = $this->contactConfig->getSettings();
        $settings['address'] = $formatter->rewriteInternalUri($settings['address']);
        $settings['disclaimer'] = $formatter->rewriteInternalUri($settings['disclaimer']);
        $this->view->assign('sidebar_contact', $settings);

        $this->setLayout('Contact/Sidebar/index.index.tpl');
    }

}