<?php
namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\SchemaInterface;

/**
 * Interface InstallerInterface
 * @package ACP3\Core\Modules
 */
interface InstallerInterface
{
    /**
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function install(SchemaInterface $schema);

    /**
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function uninstall(SchemaInterface $schema);
}