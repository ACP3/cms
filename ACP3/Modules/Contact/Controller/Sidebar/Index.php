<?php

namespace ACP3\Modules\Contact\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Class Index
 * @package ACP3\Modules\Contact\Controller\Sidebar
 */
class Index extends Core\Modules\Controller\Sidebar
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $contactConfig;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Config $contactConfig)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

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