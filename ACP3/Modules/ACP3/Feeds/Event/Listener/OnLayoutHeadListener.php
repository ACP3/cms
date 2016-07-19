<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Event\Listener;


use ACP3\Core\View;

class OnLayoutHeadListener
{
    /**
     * @var View
     */
    private $view;

    /**
     * OnLayoutHeadListener constructor.
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function renderFeedLinkTags()
    {
        echo  $this->view->fetchTemplate('Feeds/Partials/feed_link_tags.tpl');
    }
}
