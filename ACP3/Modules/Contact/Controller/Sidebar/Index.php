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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        \Doctrine\DBAL\Connection $db)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->db = $db;
    }

    public function actionIndex()
    {
        $formatter = $this->get('core.helpers.string.formatter');

        $config = new Core\Config($this->db, 'contact');
        $settings = $config->getSettings();
        $settings['address'] = $formatter->rewriteInternalUri($settings['address']);
        $settings['disclaimer'] = $formatter->rewriteInternalUri($settings['disclaimer']);
        $this->view->assign('sidebar_contact', $settings);

        $this->setLayout('Contact/Sidebar/index.index.tpl');
    }

}