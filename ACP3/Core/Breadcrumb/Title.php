<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Title
 * @package ACP3\Core\Breadcrumb\Breadcrumb
 */
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
    protected $siteTitle = '';

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
    public function getPageTitle()
    {
        if (empty($this->pageTitle)) {
            $steps = $this->steps->getBreadcrumb();
            $lastCrumb = end($steps);

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
     * Returns the title of the current page + the site title
     *
     * @return string
     */
    public function getSiteAndPageTitle()
    {
        $this->eventDispatcher->dispatch(
            'core.breadcrumb.title.get_site_and_page_title_before',
            new GetSiteAndPageTitleBeforeEvent($this)
        );

        $title = $this->getPageTitle();

        $separator = $this->getPageTitleSeparator();
        if (!empty($this->pageTitlePrefix)) {
            $title = $this->pageTitlePrefix . $separator . $title;
        }
        if (!empty($this->getPageTitlePostfix())) {
            $title .= $separator . $this->getPageTitlePostfix();
        }
        if (!empty($this->getSiteTitle())) {
            $title .= ' | ' . $this->getSiteTitle();
        }

        return $title;
    }
}
