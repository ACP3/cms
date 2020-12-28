<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

interface SortingAwareInterface
{
    /**
     * Changes the order of results within the database. The given ID gets moved one step upwards.
     */
    public function moveUp(int $id): void;

    /**
     * Changes the order of results within the database. The given ID gets moved one step downwards.
     */
    public function moveDown(int $id): void;
}
