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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\Search\Helpers
     */
    protected $searchHelpers;

    /**
     * @param \ACP3\Core\Context           $context
     * @param \ACP3\Core\Helpers\Secure    $secureHelper
     * @param \ACP3\Modules\Search\Helpers $searchHelpers
     */
    public function __construct(
        Core\Context $context,
        Core\Helpers\Secure $secureHelper,
        Search\Helpers $searchHelpers)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->searchHelpers = $searchHelpers;
    }

    public function actionIndex()
    {
        $this->view->assign('search_mods', $this->searchHelpers->getModules());

        $this->secureHelper->generateFormToken('search/index/index');

        $this->setTemplate('Search/Sidebar/index.index.tpl');
    }
}
