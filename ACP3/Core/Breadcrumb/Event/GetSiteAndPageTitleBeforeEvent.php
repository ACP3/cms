<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb\Event;


use ACP3\Core\Breadcrumb\Title;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetSiteAndPageTitleBeforeEvent
 * @package ACP3\Core\Breadcrumb\Event
 */
class GetSiteAndPageTitleBeforeEvent extends Event
{
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;

    /**
     * GetSiteAndPageTitleBeforeEvent constructor.
     *
     * @param \ACP3\Core\Breadcrumb\Title $title
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
