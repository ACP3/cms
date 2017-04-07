<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Assets\Event;

use ACP3\Core\Assets\Libraries;
use Symfony\Component\EventDispatcher\Event;

class AddLibraryEvent extends Event
{
    /**
     * @var Libraries
     */
    private $libraries;

    /**
     * AddLibraryEvent constructor.
     * @param Libraries $libraries
     */
    public function __construct(Libraries $libraries)
    {
        $this->libraries = $libraries;
    }

    /**
     * @param string $identifier
     * @param array $library
     * @return $this
     */
    public function addLibrary($identifier, array $library)
    {
        $this->libraries->addLibrary($identifier, $library);

        return $this;
    }
}
