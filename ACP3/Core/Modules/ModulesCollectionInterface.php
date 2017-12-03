<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


interface ModulesCollectionInterface
{
    /**
     * Returns a collection of ACP3 modules
     *
     * @return array
     */
    public function getAll(): array;
}
