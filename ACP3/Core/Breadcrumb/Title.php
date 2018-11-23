<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Title
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $steps;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $pageTitle = '';
    /**
     * @var string
     */
    protected $pageTitlePostfix = '';
    /**
     * @var string
     */
    protected $pageTitlePrefix = '';
    /**
     * @var string
     */
    protected $pageTitleSeparator = '-';
    /**
     * @var string
     */
    private $metaTitle = '';
    /**
     * @var string
     */
    protected $siteTitleSeparator = '|';
    /**
     * @var string
     */
    protected $siteTitle = '';
    /**
     * @var string|null
     */
    private $siteSubtitle;

    /**
     * Title constructor.
     *
     * @param \ACP3\Core\Breadcrumb\Steps                                 $steps
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Steps $steps, EventDispatcherInterface $eventDispatcher)
    {
        $this->steps = $steps;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->siteTitle;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setSiteTitle(string $title)
    {
        $this->siteTitle = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteSubtitle()
    {
        return $this->siteSubtitle;
    }

    /**
     * @param string $siteSubtitle
     *
     * @return $this
     */
    public function setSiteSubtitle(string $siteSubtitle)
    {
        $this->siteSubtitle = $siteSubtitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        if (empty($this->pageTitle)) {
            $steps = $this->steps->getBreadcrumb();
            $lastCrumb = \end($steps);

            $this->pageTitle = $lastCrumb['title'];
        }

        return $this->pageTitle;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPageTitle(string $title)
    {
        $this->pageTitle = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageTitlePostfix()
    {
        return $this->pageTitlePostfix;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageTitlePostfix(string $value)
    {
        $this->pageTitlePostfix = $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageTitlePrefix(string $value)
    {
        $this->pageTitlePrefix = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageTitleSeparator()
    {
        return ' ' . $this->pageTitleSeparator . ' ';
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageTitleSeparator(string $value)
    {
        $this->pageTitleSeparator = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle(string $metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiteTitleSeparator()
    {
        return ' ' . $this->siteTitleSeparator . ' ';
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSiteTitleSeparator(string $value)
    {
        $this->siteTitleSeparator = $value;

        return $this;
    }

    /**
     * Returns the title of the current page + the site title.
     *
     * @return string
     */
    public function getSiteAndPageTitle()
    {
        $this->eventDispatcher->dispatch(
            'core.breadcrumb.title.get_site_and_page_title_before',
            new GetSiteAndPageTitleBeforeEvent($this)
        );

        $title = !empty($this->getMetaTitle()) ? $this->getMetaTitle() : $this->getPageTitle();

        if (!empty($this->pageTitlePrefix)) {
            $title = $this->pageTitlePrefix . $this->getPageTitleSeparator() . $title;
        }
        if (!empty($this->getPageTitlePostfix())) {
            $title .= $this->getPageTitleSeparator() . $this->getPageTitlePostfix();
        }
        if (!empty($this->getSiteTitle())) {
            $title .= $this->getSiteTitleSeparator() . $this->getSiteTitle();
        }
        if (!empty($this->getSiteSubtitle())) {
            $title .= $this->getPageTitleSeparator() . $this->getSiteSubtitle();
        }

        return $title;
    }
}
