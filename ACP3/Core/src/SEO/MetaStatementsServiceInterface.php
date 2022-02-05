<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\SEO;

interface MetaStatementsServiceInterface
{
    public const INDEX_FOLLOW = 'index,follow';
    public const INDEX_NOFOLLOW = 'index,nofollow';
    public const NOINDEX_FOLLOW = 'noindex,follow';
    public const NOINDEX_NOFOLLOW = 'noindex,nofollow';

    /**
     * @return static
     */
    public function setPageRobotsSettings(string $metaRobots): self;

    /**
     * Returns the meta tags of the current page.
     *
     * @return array<string, mixed>
     */
    public function getMetaTags(): array;

    /**
     * Returns the SEO description of the current page.
     */
    public function getPageDescription(): string;

    /**
     * Returns the SEO description of the given page.
     */
    public function getDescription(string $path): string;

    /**
     * Returns the JSON-LD structured data of the given page.
     */
    public function getStructuredData(string $path): string;

    /**
     * Returns the SEO keywords of the current page.
     */
    public function getPageKeywords(): string;

    /**
     * Returns the SEO keywords of the given page.
     */
    public function getKeywords(string $path): string;

    /**
     * Returns the meta title of the given page.
     */
    public function getTitle(string $path): string;

    public function getSeoInformation(string $path, string $key, string $defaultValue = ''): string;

    /**
     * Returns the SEO robots setting for the current page.
     */
    public function getPageRobotsSetting(): string;

    /**
     * Returns the SEO robots settings for the given page.
     */
    public function getRobotsSetting(string $path = ''): string;

    /**
     * @return array<int, string>
     */
    public function getRobotsMap(): array;

    /**
     * Sets a SEO description postfix for te current page.
     *
     * @return $this
     */
    public function setDescriptionPostfix(string $value): static;

    /**
     * Sets the canonical URL for the current page.
     *
     * @return $this
     */
    public function setCanonicalUri(string $path): static;

    /**
     * Sets the next page (useful for pagination).
     *
     * @return $this
     */
    public function setNextPage(string $path): static;

    /**
     * Sets the previous page (useful for pagination).
     *
     * @return $this
     */
    public function setPreviousPage(string $path): static;
}
