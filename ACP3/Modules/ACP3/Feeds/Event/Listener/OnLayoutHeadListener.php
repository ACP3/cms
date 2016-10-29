<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Event\Listener;


use ACP3\Core\View;
use ACP3\Modules\ACP3\Feeds\Utility\AvailableFeedsRegistrar;

class OnLayoutHeadListener
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var AvailableFeedsRegistrar
     */
    private $availableFeedsRegistrar;

    /**
     * OnLayoutHeadListener constructor.
     * @param View $view
     * @param AvailableFeedsRegistrar $availableFeedsRegistrar
     */
    public function __construct(
        View $view,
        AvailableFeedsRegistrar $availableFeedsRegistrar
    ) {
        $this->view = $view;
        $this->availableFeedsRegistrar = $availableFeedsRegistrar;
    }

    public function renderFeedLinkTags()
    {
        $this->view->assign('available_feeds', $this->availableFeedsRegistrar->getAvailableModuleNames());

        $this->view->displayTemplate('Feeds/Partials/feed_link_tags.tpl');
    }
}
