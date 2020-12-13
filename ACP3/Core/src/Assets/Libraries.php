<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Dto\LibraryDto;
use ACP3\Core\Assets\Event\AddLibraryEvent;
use ACP3\Core\Http\RequestInterface;
use MJS\TopSort\Implementations\StringSort;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Libraries
{
    /**
     * @var Array<string, LibraryDto>
     */
    private $libraries = [];
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request;

        $this
            ->addLibrary(
                new LibraryDto(
                    'polyfill',
                    true,
                    false,
                    [],
                    [],
                    ['polyfill.min.js']
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'jquery',
                    true,
                    false,
                    [],
                    [],
                    ['jquery.min.js']
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'font-awesome',
                    true,
                    false,
                    [],
                    ['all.css'],
                    [],
                    null,
                    true
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'js-cookie',
                    false,
                    false,
                    [],
                    [],
                    ['js.cookie.js']
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'moment',
                    false,
                    false,
                    [],
                    [],
                    ['moment.min.js']
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'bootstrap',
                    false,
                    false,
                    ['jquery'],
                    ['bootstrap.min.css'],
                    ['bootstrap.min.js']
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'ajax-form',
                    true,
                    false,
                    ['bootstrap', 'jquery'],
                    ['loading-layer.min.css'],
                    ['partials/ajax-form.js'],
                    null,
                    true
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'bootbox',
                    false,
                    false,
                    ['bootstrap'],
                    [],
                    ['bootbox.all.min.js']
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'datatables',
                    false,
                    false,
                    ['bootstrap'],
                    ['dataTables.bootstrap.css'],
                    ['jquery.dataTables.js'],
                    null,
                    true
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'datetimepicker',
                    false,
                    false,
                    ['jquery', 'moment'],
                    ['bootstrap-datetimepicker.css'],
                    ['bootstrap-datetimepicker.min.js'],
                    null,
                    true
                )
            )
            ->addLibrary(
                new LibraryDto(
                    'fancybox',
                    false,
                    false,
                    ['jquery'],
                    ['jquery.fancybox.css'],
                    ['jquery.fancybox.min.js'],
                    null,
                    true
                )
    );
    }

    public function dispatchAddLibraryEvent(): void
    {
        $this->eventDispatcher->dispatch(new AddLibraryEvent($this), AddLibraryEvent::NAME);
    }

    /**
     * @return Array<string, LibraryDto>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getLibraries(): array
    {
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
     * @param string|\ACP3\Core\Assets\Dto\LibraryDto $library
     *
     * @return $this
     */
    public function addLibrary($library, ?array $options = null): self
    {
        if (\is_string($library)) {
            if ($options === null || empty($options)) {
                throw new \InvalidArgumentException(\sprintf('You need to pass a valid options array for this asset library %s', $library));
            }

            $library = new LibraryDto(
                $library,
                $options['enabled'] ?? false,
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

        if ($library->isEnabled()) {
            $this->enableLibraries($library->getDependencies());
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
        foreach ($libraries as $library) {
            if (\array_key_exists($library, $this->libraries) === true) {
                // Resolve javascript library dependencies recursively
                if (!empty($this->libraries[$library]->getDependencies())) {
                    $this->enableLibraries($this->libraries[$library]->getDependencies());
                }

                // Enable the javascript library
                $this->libraries[$library] = $this->libraries[$library]->enable();
            } else {
                throw new \InvalidArgumentException(\sprintf('Could not find library %s', $library));
            }
        }

        return $this;
    }

    /**
     * @return string[]
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

            $enabledLibraries[] = $libraryName;
        }

        return $enabledLibraries;
    }

    private function includeInXmlHttpRequest(LibraryDto $library): bool
    {
        return $this->request->isXmlHttpRequest()
            && $library->isEnabledForAjax() === false;
    }
}
