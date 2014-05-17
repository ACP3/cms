<?php
namespace ACP3\Core\Modules;

interface InstallerInterface
{
    /**
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(\Doctrine\DBAL\Connection $db);
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