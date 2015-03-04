<?php

namespace ACP3\Modules\ACP3\Search\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\ACP3\Search;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Search\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Modules\ACP3\Search\Helpers
     */
    protected $searchHelpers;

    /**
     * @param \ACP3\Core\Context           $context
     * @param \ACP3\Modules\ACP3\Search\Helpers $searchHelpers
     */
    public function __construct(
        Core\Context $context,
        Search\Helpers $searchHelpers)
    {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
    }

    public function actionIndex()
    {
        $this->view->assign('search_mods', $this->searchHelpers->getModules());

        $this->setTemplate('Search/Sidebar/index.index.tpl');
    }
}
