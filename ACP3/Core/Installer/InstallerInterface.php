<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer;

interface InstallerInterface
{
    /**
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function install(SchemaInterface $schema);

    /**
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function uninstall(SchemaInterface $schema);
}
