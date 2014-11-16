<?php
namespace ACP3\Core\Modules;

/**
 * Interface InstallerInterface
 * @package ACP3\Core\Modules
 */
interface InstallerInterface
{
    /**
     * Liefert ein Array mit den zu erstellenden Datenbanktabellen des Moduls zurück
     */
    public function createTables();

    /**
     * Liefert ein Array mit den zu löschenden Datenbanktabellen des Moduls zurück
     */
    public function removeTables();

    /**
     * Liefert ein Array mit den zu erstellenden Moduleinstellungen zurück
     */
    public function settings();

    /**
     * Aktualisiert die Tabellen und Einstellungen eines Moduls auf eine neue Version
     */
    public function schemaUpdates();
}