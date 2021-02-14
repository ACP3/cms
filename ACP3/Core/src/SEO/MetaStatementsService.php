<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\SEO;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;

class MetaStatementsService implements MetaStatementsServiceInterface
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $nextPage = '';
    /**
     * @var string
     */
    private $previousPage = '';
    /**
     * @var string
     */
    private $canonicalUrl = '';
    /**
     * @var string
     */
    private $metaDescriptionPostfix = '';
    /**
     * @var string
     */
    private $metaRobots = '';

    private static $robotSettingsMaps = [
        1 => 'index,follow',
        2 => 'index,nofollow',
        3 => 'noindex,follow',
        4 => 'noindex,nofollow',
    ];

    public function __construct(
        RequestInterface $request,
        RouterInterface $router
    ) {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @return $this
     */
    public function setPageRobotsSettings(string $metaRobots)
    {
        $this->metaRobots = $metaRobots;

        return $this;
    }

    /**
     * Returns the meta tags of the current page.
     */
    public function getMetaTags(): array
    {
        $this->addSelfReferencingCanonical();

        return [
            'description' => $this->isInAdmin() ? '' : $this->getPageDescription(),
            'keywords' => $this->isInAdmin() ? '' : $this->getPageKeywords(),
            'robots' => $this->isInAdmin() ? 'noindex,nofollow' : $this->getPageRobotsSetting(),
            'previous_page' => $this->previousPage,
            'next_page' => $this->nextPage,
            'canonical' => $this->canonicalUrl,
        ];
    }

    private function addSelfReferencingCanonical(): void
    {
        if ($this->isInAdmin()) {
            return;
        }
        if (!empty($this->canonicalUrl)) {
            return;
        }
        if (\strpos($this->request->getQuery(), 'errors/') === 0) {
            return;
        }

        $this->canonicalUrl = $this->router->route($this->request->getQuery());
    }

    private function isInAdmin(): bool
    {
        return $this->request->getArea() === AreaEnum::AREA_ADMIN;
    }

    /**
     * Returns the SEO description of the current page.
     */
    public function getPageDescription(): string
    {
        $description = $this->getDescription($this->request->getUriWithoutPages());

        if (empty($description)) {
            $description = $this->getDescription($this->request->getFullPath());
        }
        if (empty($description)) {
            $description = $this->getDescription($this->request->getModule());
        }
        if (empty($description)) {
            $description = $this->getSettings()['meta_description'];
        }

        $postfix = '';
        if (!empty($description) && !empty($this->metaDescriptionPostfix)) {
            $postfix .= ' - ' . $this->metaDescriptionPostfix;
        }

        return $description . $postfix;
    }

    protected function getSettings(): array
    {
        return [
            'meta_description' => '',
            'meta_keywords' => '',
            'robots' => 1,
        ];
    }

    /**
     * Returns the SEO description of the given page.
     */
    public function getDescription(string $path): string
    {
        return $this->getSeoInformation($path, 'description');
    }

    /**
     * Returns the SEO keywords of the current page.
     */
    public function getPageKeywords(): string
    {
        $keywords = $this->getKeywords($this->request->getUriWithoutPages());
        if (empty($keywords)) {
            $keywords = $this->getKeywords($this->request->getFullPath());
        }
        if (empty($keywords)) {
            $keywords = $this->getKeywords($this->request->getModule());
        }

        return \strtolower(!empty($keywords) ? $keywords : $this->getSettings()['meta_keywords']);
    }

    /**
     * Returns the SEO keywords of the given page.
     */
    public function getKeywords(string $path): string
    {
        return $this->getSeoInformation($path, 'keywords');
    }

    /**
     * Returns the meta title of the given page.
     */
    public function getTitle(string $path): string
    {
        return $this->getSeoInformation($path, 'title');
    }

    public function getSeoInformation(string $path, string $key, string $defaultValue = ''): string
    {
        return $defaultValue;
    }

    /**
     * Returns the SEO robots setting for the current page.
     */
    public function getPageRobotsSetting(): string
    {
        if (!empty($this->metaRobots)) {
            return $this->metaRobots;
        }

        $robots = $this->getRobotsSetting($this->request->getUriWithoutPages());
        if (empty($robots)) {
            $robots = $this->getRobotsSetting($this->request->getFullPath());
        }
        if (empty($robots)) {
            $robots = $this->getRobotsSetting($this->request->getModule());
        }

        return \strtolower(!empty($robots) ? $robots : $this->getRobotsSetting());
    }

    /**
     * Returns the SEO robots settings for the given page.
     */
    public function getRobotsSetting(string $path = ''): string
    {
        if ($path === '') {
            return \strtr($this->getSettings()['robots'], $this->getRobotsMap());
        }

        $robot = $this->getSeoInformation($path, 'robots', '0');

        if ($robot === '0') {
            $robot = $this->getSettings()['robots'];
        }

        return \strtr($robot, $this->getRobotsMap());
    }

    public function getRobotsMap(): array
    {
        return self::$robotSettingsMaps;
    }

    /**
     * Sets a SEO description postfix for te current page.
     *
     * @return $this
     */
    public function setDescriptionPostfix(string $value)
    {
        $this->metaDescriptionPostfix = $value;

        return $this;
    }

    /**
     * Sets the canonical URL for the current page.
     *
     * @return $this
     */
    public function setCanonicalUri(string $path)
    {
        $this->canonicalUrl = $path;

        return $this;
    }

    /**
     * Sets the next page (useful for pagination).
     *
     * @return $this
     */
    public function setNextPage(string $path)
    {
        $this->nextPage = $path;

        return $this;
    }

    /**
     * Sets the previous page (useful for pagination).
     *
     * @return $this
     */
    public function setPreviousPage(string $path)
    {
        $this->previousPage = $path;

        return $this;
    }
}
