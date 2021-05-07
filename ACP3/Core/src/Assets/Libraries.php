<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Assets\Event\AddLibraryEvent;
use MJS\TopSort\Implementations\StringSort;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Libraries
{
    /**
     * @var Array<string, \ACP3\Core\Assets\Entity\LibraryEntity>
     */
    private $libraries = [];
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    /**
     * @var \ACP3\Core\Assets\LibrariesCache
     */
    private $librariesCache;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LibrariesCache $librariesCache
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->librariesCache = $librariesCache;
    }

    /**
     * @deprecated To be removed with version 6.0.0. Register the libraries using the DI container compiler pass instead.
     */
    public function dispatchAddLibraryEvent(): void
    {
        $this->eventDispatcher->dispatch(new AddLibraryEvent($this), AddLibraryEvent::NAME);
    }

    /**
     * @return Array<string, LibraryEntity>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getLibraries(): array
    {
        if (!$this->libraries) {
            return [];
        }

        $topSort = new StringSort();
        foreach ($this->libraries as $libraryName => $options) {
            $topSort->add($libraryName, $options->getDependencies());
        }

        $librariesTopSorted = [];
        foreach ($topSort->sort() as $libraryName) {
            $librariesTopSorted[$libraryName] = $this->libraries[$libraryName];
        }

        return $librariesTopSorted;
    }

    /**
     * @param string|\ACP3\Core\Assets\Entity\LibraryEntity $library
     *
     * @return $this
     */
    public function addLibrary($library, ?array $options = null): self
    {
        if (\is_string($library)) {
            if ($options === null || empty($options)) {
                throw new \InvalidArgumentException(sprintf('You need to pass a valid options array for this asset library %s', $library));
            }

            $library = new LibraryEntity(
                $library,
                $options['enabled_for_ajax'] ?? false,
                $options['dependencies'] ?? [],
                \array_key_exists('css', $options) && \is_string($options['css']) ? [$options['css']] : $options['css'] ?? [],
                \array_key_exists('js', $options) && \is_string($options['js']) ? [$options['js']] : $options['js'] ?? [],
                $options['module'] ?? null
            );
        }

        if (!isset($this->libraries[$library->getLibraryIdentifier()])) {
            $this->libraries[$library->getLibraryIdentifier()] = $library;
        }

        return $this;
    }

    /**
     * Activates frontend libraries.
     *
     * @return $this
     */
    public function enableLibraries(array $libraries): self
    {
        foreach ($libraries as $libraryIdentifier) {
            if (\array_key_exists($libraryIdentifier, $this->libraries) === false) {
                throw new \InvalidArgumentException(sprintf('Could not find library %s', $libraryIdentifier));
            }

            // Resolve javascript library dependencies recursively
            if (!empty($this->libraries[$libraryIdentifier]->getDependencies())) {
                $this->enableLibraries($this->libraries[$libraryIdentifier]->getDependencies());
            }

            // Enable the javascript library
            $this->libraries[$libraryIdentifier] = $this->libraries[$libraryIdentifier]->enable();

            if ($this->requestStack->getCurrentRequest()) {
                $this->librariesCache->scheduleStoreEnabledLibraryInCache($this->requestStack->getCurrentRequest(), $libraryIdentifier);
            }
        }

        return $this;
    }

    /**
     * @return Array<string, LibraryEntity>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getEnabledLibraries(): array
    {
        $enabledLibraries = [];
        foreach ($this->getLibraries() as $libraryName => $options) {
            if ($this->includeInXmlHttpRequest($options)) {
                continue;
            }
            if ($options->isEnabled() === false) {
                continue;
            }

            $enabledLibraries[$libraryName] = $options;
        }

        return $enabledLibraries;
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getEnabledLibrariesAsString(): string
    {
        return implode(',', array_keys($this->getEnabledLibraries()));
    }

    private function getMasterRequest(): Request
    {
        return $this->requestStack->getMasterRequest();
    }

    private function includeInXmlHttpRequest(LibraryEntity $library): bool
    {
        return $this->getMasterRequest()->isXmlHttpRequest()
            && $library->isEnabledForAjax() === false;
    }
}
