<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Event;

use ACP3\Core\Assets\Libraries;
use Symfony\Contracts\EventDispatcher\Event;

class AddLibraryEvent extends Event
{
    public const NAME = 'core.assets.add_libraries';

    /**
     * @var Libraries
     */
    private $libraries;

    public function __construct(Libraries $libraries)
    {
        $this->libraries = $libraries;
    }

    /**
     * @return $this
     */
    public function addLibrary(string $identifier, array $library): self
    {
        $this->libraries->addLibrary($identifier, $library);

        return $this;
    }
}
