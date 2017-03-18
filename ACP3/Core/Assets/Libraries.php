<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Event\AddLibraryEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Libraries
{
    /**
     * @var array
     */
    protected $libraries = [
        'moment' => [
            'enabled' => false,
            'js' => 'moment.min.js'
        ],
        'jquery' => [
            'enabled' => true,
            'js' => 'jquery.min.js'
        ],
        'js-cookie' => [
            'enabled' => true,
            'js' => 'js.cookie.js',
        ],
        'fancybox' => [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'jquery.fancybox.css',
            'js' => 'jquery.fancybox.pack.js'
        ],
        'bootstrap' => [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'bootstrap.min.css',
            'js' => 'bootstrap.min.js'
        ],
        'datatables' => [
            'enabled' => false,
            'dependencies' => ['bootstrap'],
            'css' => 'dataTables.bootstrap.css',
            'js' => 'jquery.dataTables.js'
        ],
        'bootbox' => [
            'enabled' => false,
            'dependencies' => ['bootstrap'],
            'js' => 'bootbox.js'
        ],
        'datetimepicker' => [
            'enabled' => false,
            'dependencies' => ['jquery', 'moment'],
            'css' => 'bootstrap-datetimepicker.css',
            'js' => 'bootstrap-datetimepicker.min.js'
        ],
    ];
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Libraries constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchAddLibraryEvent()
    {
        $this->eventDispatcher->dispatch('core.assets.add_libraries', new AddLibraryEvent($this));
    }

    /**
     * @return array
     */
    public function getLibraries()
    {
        return $this->libraries;
    }

    /**
     * @param string $identifier
     * @param array $options
     * @return $this
     */
    public function addLibrary($identifier, array $options)
    {
        if (!isset($this->libraries[$identifier])) {
            $this->libraries[$identifier] = $options;
        }

        return $this;
    }

    /**
     * Activates frontend libraries
     *
     * @param array $libraries
     *
     * @return $this
     */
    public function enableLibraries(array $libraries)
    {
        foreach ($libraries as $library) {
            if (array_key_exists($library, $this->libraries) === true) {
                // Resolve javascript library dependencies recursively
                if (!empty($this->libraries[$library]['dependencies'])) {
                    $this->enableLibraries($this->libraries[$library]['dependencies']);
                }

                // Enable the javascript library
                $this->libraries[$library]['enabled'] = true;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getEnabledLibraries()
    {
        $enabledLibraries = [];
        foreach ($this->libraries as $library => $values) {
            if ($values['enabled'] === true) {
                $enabledLibraries[] = $library;
            }
        }

        return $enabledLibraries;
    }
}
