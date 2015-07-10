<?php
namespace ACP3\Core\Modules\Installer;

/**
 * Interface MigrationInterface
 * @package ACP3\Core\Modules\Installer
 */
interface MigrationInterface
{
    /**
     * Aktualisiert die Tabellen und Einstellungen eines Moduls auf eine neue Version
     *
     * @return array
     */
    public function schemaUpdates();

    /**
     * Methodenstub zum Umbenennen eines Moduls
     *
     * @return array
     */
    public function renameModule();

}