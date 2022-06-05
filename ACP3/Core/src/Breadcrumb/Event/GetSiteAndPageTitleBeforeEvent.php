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

    public function __construct(private readonly Title $title)
    {
    }

    public function getTitle(): Title
    {
        return $this->title;
    }
}
