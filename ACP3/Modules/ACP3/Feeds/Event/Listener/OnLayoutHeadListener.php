<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Event\Listener;


use ACP3\Core\View;
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
     * OnLayoutHeadListener constructor.
     * @param View $view
     * @param FeedAvailabilityRegistrar $availableFeedsRegistrar
     */
    public function __construct(
        View $view,
        FeedAvailabilityRegistrar $availableFeedsRegistrar
    ) {
        $this->view = $view;
        $this->availableFeedsRegistrar = $availableFeedsRegistrar;
    }

    public function renderFeedLinks()
    {
        $this->view->assign('available_feeds', $this->availableFeedsRegistrar->getAvailableModuleNames());

        $this->view->displayTemplate('Feeds/Partials/head.feed_links.tpl');
    }
}
