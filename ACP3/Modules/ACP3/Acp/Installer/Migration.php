<?php
namespace ACP3\Modules\ACP3\Acp\Installer;

use ACP3\Core\Modules\Installer\MigrationInterface;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Acp\Installer
 */
class Migration implements MigrationInterface
{

    /**
     * Aktualisiert die Tabellen und Einstellungen eines Moduls auf eine neue Version
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [];
    }

    /**
     * Methodenstub zum Umbenennen eines Moduls
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}