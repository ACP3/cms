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
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;

    public function __construct(
        Core\Context $context,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
    }

    public function actionIndex()
    {
        $this->view->assign('search_mods', $this->get('search.helpers')->getModules());

        $this->secureHelper->generateFormToken('search/index/index');

        $this->setLayout('Search/Sidebar/index.index.tpl');
    }

}