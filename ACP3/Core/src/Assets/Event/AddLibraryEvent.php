<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Event;

use ACP3\Core\Assets\Libraries;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated To be removed with version 6.x. Use the DI tag `acp3.assets.library` instead.
 */
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
     * @param string|\ACP3\Core\Assets\Entity\LibraryEntity $library
     *
     * @return $this
     */
    public function addLibrary($library, ?array $options = null): self
    {
        $this->libraries->addLibrary($library, $options);

        return $this;
    }
}
