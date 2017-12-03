<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


class AllModules implements ModulesCollectionInterface
{
    /**
     * @var Modules
     */
    private $modules;

    /**
     * @param Modules $modules
     */
    public function __construct(Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function getAll(): array
    {
        return $this->modules->getAllModules();
    }
}
