<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Repository;

use function RFM\app;

/**
 *    BaseItemModel PHP class.
 *
 *    Base class created to define base methods
 *
 *    @license    MIT License
 *    @author        Pavel Solomienko <https://github.com/servocoder/>
 *    @copyright    Authors
 */
abstract class BaseItemModel implements ItemModelInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Get storage instance associated with model item.
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Associate storage with model item.
     */
    public function setStorage(string $storageName): void
    {
        $this->storage = app()->getStorage($storageName);
    }
}
