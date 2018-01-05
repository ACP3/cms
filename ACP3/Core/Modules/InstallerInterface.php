<?php
namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\SchemaInterface;

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
