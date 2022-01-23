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
    private string $nextPage = '';

    private string $previousPage = '';

    private string $canonicalUrl = '';

    private string $metaDescriptionPostfix = '';

    private string $metaRobots = '';

    /**
     * @var array<int, string>
     */
    private static array $robotSettingsMaps = [
        1 => MetaStatementsServiceInterface::INDEX_FOLLOW,
        2 => MetaStatementsServiceInterface::INDEX_NOFOLLOW,
        3 => MetaStatementsServiceInterface::NOINDEX_FOLLOW,
        4 => MetaStatementsServiceInterface::NOINDEX_NOFOLLOW,
    ];

    public function __construct(private RequestInterface $request, private RouterInterface $router)
    {
    }

    /**
     * @return $this
     */
    public function setPageRobotsSettings(string $metaRobots): MetaStatementsServiceInterface
    {
        $this->metaRobots = $metaRobots;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaTags(): array
    {
        $this->addSelfReferencingCanonical();

        return [
            'description' => $this->isInAdmin() ? '' : $this->getPageDescription(),
            'keywords' => $this->isInAdmin() ? '' : $this->getPageKeywords(),
            'robots' => $this->isInAdmin() ? MetaStatementsServiceInterface::NOINDEX_NOFOLLOW : $this->getPageRobotsSetting(),
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
        if (str_starts_with($this->request->getQuery(), 'errors/')) {
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

    /**
     * @return array<string, mixed>
     */
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

        return strtolower(!empty($keywords) ? $keywords : $this->getSettings()['meta_keywords']);
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

        return strtolower(!empty($robots) ? $robots : $this->getRobotsSetting());
    }

    /**
     * Returns the SEO robots settings for the given page.
     */
    public function getRobotsSetting(string $path = ''): string
    {
        if ($path === '') {
            return strtr($this->getSettings()['robots'], $this->getRobotsMap());
        }

        $robot = $this->getSeoInformation($path, 'robots', '0');

        if ($robot === '0') {
            $robot = $this->getSettings()['robots'];
        }

        return strtr($robot, $this->getRobotsMap());
    }

    /**
     * {@inheritDoc}
     */
    public function getRobotsMap(): array
    {
        return self::$robotSettingsMaps;
    }

    /**
     * Sets a SEO description postfix for te current page.
     *
     * @return static
     */
    public function setDescriptionPostfix(string $value): self
    {
        $this->metaDescriptionPostfix = $value;

        return $this;
    }

    /**
     * Sets the canonical URL for the current page.
     *
     * @return static
     */
    public function setCanonicalUri(string $path): self
    {
        $this->canonicalUrl = $path;

        return $this;
    }

    /**
     * Sets the next page (useful for pagination).
     *
     * @return static
     */
    public function setNextPage(string $path): self
    {
        $this->nextPage = $path;

        return $this;
    }

    /**
     * Sets the previous page (useful for pagination).
     *
     * @return static
     */
    public function setPreviousPage(string $path): self
    {
        $this->previousPage = $path;

        return $this;
    }
}
