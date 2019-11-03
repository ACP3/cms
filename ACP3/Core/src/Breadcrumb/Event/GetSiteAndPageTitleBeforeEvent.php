<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Breadcrumb\Event;

use ACP3\Core\Breadcrumb\Title;
use Symfony\Contracts\EventDispatcher\Event;

class GetSiteAndPageTitleBeforeEvent extends Event
{
    public const NAME = 'core.breadcrumb.title.get_site_and_page_title_before';

    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;

    /**
     * GetSiteAndPageTitleBeforeEvent constructor.
     */
    public function __construct(Title $title)
    {
        $this->title = $title;
    }

    /**
     * @return \ACP3\Core\Breadcrumb\Title
     */
    public function getTitle()
    {
        return $this->title;
    }
}
