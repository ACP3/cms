<?php

namespace ACP3\Modules\Search\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Search;

/**
 * Class Index
 * @package ACP3\Modules\Search\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Session
     */
    protected $session;

    public function __construct(
        Core\Context $context,
        Core\Session $session)
    {
        parent::__construct($context);

        $this->session = $session;
    }

    public function actionIndex()
    {
        $this->view->assign('search_mods', $this->get('search.helpers')->getModules());

        $this->session->generateFormToken('search/index/index');

        $this->setLayout('Search/Sidebar/index.index.tpl');
    }

}