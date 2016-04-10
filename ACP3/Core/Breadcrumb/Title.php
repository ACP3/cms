<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb;

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
     * @param \ACP3\Core\Breadcrumb\Steps $steps
     */
    public function __construct(Steps $steps)
    {
        $this->steps = $steps;
    }

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->siteTitle;
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
        $title = $this->getPageTitle();

        $separator = $this->getPageTitleSeparator();
        if (!empty($this->pageTitlePrefix)) {
            $title = $this->pageTitlePrefix . $separator . $title;
        }
        if (!empty($this->pageTitlePostfix)) {
            $title .= $separator . $this->pageTitlePostfix;
        }
        $title .= ' | ' . $this->getSiteTitle();

        return $title;
    }
}
