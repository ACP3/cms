<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Event\Listener;

use ACP3\Core\Modules\Modules;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Feeds\Installer\Schema;
use ACP3\Modules\ACP3\Feeds\Utility\FeedAvailabilityRegistrar;

class OnLayoutHeadListener
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var FeedAvailabilityRegistrar
     */
    private $availableFeedsRegistrar;
    /**
     * @var Modules
     */
    private $modules;

    /**
     * OnLayoutHeadListener constructor.
     *
     * @param View                      $view
     * @param Modules                   $modules
     * @param FeedAvailabilityRegistrar $availableFeedsRegistrar
     */
    public function __construct(
        View $view,
        Modules $modules,
        FeedAvailabilityRegistrar $availableFeedsRegistrar
    ) {
        $this->view = $view;
        $this->availableFeedsRegistrar = $availableFeedsRegistrar;
        $this->modules = $modules;
    }

    public function renderFeedLinks()
    {
        if ($this->modules->isActive(Schema::MODULE_NAME)) {
            $this->view->assign('available_feeds', $this->availableFeedsRegistrar->getAvailableModuleNames());

            $this->view->displayTemplate('Feeds/Partials/head.feed_links.tpl');
        }
    }
}
