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
    protected string $pageTitle = '';

    protected string $pageTitlePostfix = '';

    protected string $pageTitlePrefix = '';

    protected string $pageTitleSeparator = '-';

    private string $metaTitle = '';

    protected string $siteTitleSeparator = '|';

    protected string $siteTitle = '';

    private ?string $siteSubtitle = null;

    public function __construct(protected Steps $steps, protected EventDispatcherInterface $eventDispatcher)
    {
    }

    public function getSiteTitle(): string
    {
        return $this->siteTitle;
    }

    /**
     * @return static
     */
    public function setSiteTitle(string $title): self
    {
        $this->siteTitle = $title;

        return $this;
    }

    public function getSiteSubtitle(): ?string
    {
        return $this->siteSubtitle;
    }

    /**
     * @return static
     */
    public function setSiteSubtitle(string $siteSubtitle): self
    {
        $this->siteSubtitle = $siteSubtitle;

        return $this;
    }

    public function getPageTitle(): string
    {
        if (empty($this->pageTitle)) {
            $steps = $this->steps->getBreadcrumb();
            $lastCrumb = end($steps);

            if ($lastCrumb !== false) {
                $this->pageTitle = $lastCrumb['title'];
            }
        }

        return $this->pageTitle;
    }

    /**
     * @return $this
     */
    public function setPageTitle(string $title): self
    {
        $this->pageTitle = $title;

        return $this;
    }

    public function getPageTitlePostfix(): string
    {
        return $this->pageTitlePostfix;
    }

    /**
     * @return static
     */
    public function setPageTitlePostfix(string $value): self
    {
        $this->pageTitlePostfix = $value;

        return $this;
    }

    /**
     * @return static
     */
    public function setPageTitlePrefix(string $value): self
    {
        $this->pageTitlePrefix = $value;

        return $this;
    }

    public function getPageTitleSeparator(): string
    {
        return ' ' . $this->pageTitleSeparator . ' ';
    }

    /**
     * @return static
     */
    public function setPageTitleSeparator(string $value): self
    {
        $this->pageTitleSeparator = $value;

        return $this;
    }

    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    /**
     * @return static
     */
    public function setMetaTitle(string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getSiteTitleSeparator(): string
    {
        return ' ' . $this->siteTitleSeparator . ' ';
    }

    /**
     * @return static
     */
    public function setSiteTitleSeparator(string $value): self
    {
        $this->siteTitleSeparator = $value;

        return $this;
    }

    /**
     * Returns the title of the current page + the site title.
     */
    public function getSiteAndPageTitle(): string
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
