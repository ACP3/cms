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
    public function setSiteTitle($title)
    {
        $this->siteTitle = $title;

        return $this;
    }

    /**
     * @return string
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
    public function setSiteSubtitle($siteSubtitle)
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
    public function setPageTitle($title)
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
    public function setPageTitlePostfix($value)
    {
        $this->pageTitlePostfix = $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageTitlePrefix($value)
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
    public function setPageTitleSeparator($value)
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
    public function setSiteTitleSeparator($value)
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
            new GetSiteAndPageTitleBeforeEvent($this),
            GetSiteAndPageTitleBeforeEvent::NAME
        );

        return $this->renderPageTitlePrefix()
            . $this->renderPageTitle()
            . $this->renderPageTitleSuffix()
            . $this->renderSiteTitle()
            . $this->renderSiteSubTitle();
    }

    protected function renderPageTitle(): string
    {
        return !empty($this->getMetaTitle()) ? $this->getMetaTitle() : $this->getPageTitle();
    }

    protected function renderPageTitlePrefix(): string
    {
        if (!empty($this->pageTitlePrefix)) {
            return $this->pageTitlePrefix . $this->getPageTitleSeparator();
        }

        return '';
    }

    protected function renderPageTitleSuffix(): string
    {
        if (!empty($this->getPageTitlePostfix())) {
            return $this->getPageTitleSeparator() . $this->getPageTitlePostfix();
        }

        return '';
    }

    protected function renderSiteTitle(): string
    {
        if (!empty($this->getSiteTitle())) {
            return $this->getSiteTitleSeparator() . $this->getSiteTitle();
        }

        return '';
    }

    protected function renderSiteSubTitle(): string
    {
        if (!empty($this->getSiteSubtitle())) {
            return $this->getPageTitleSeparator() . $this->getSiteSubtitle();
        }

        return '';
    }
}
