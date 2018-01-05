<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules\Installer;

interface SchemaInterface
{
    /**
     * Liefert ein Array mit den zu erstellenden Datenbanktabellen des Moduls zurück
     *
     * @return array
     */
    public function createTables();

    /**
     * Liefert ein Array mit den zu löschenden Datenbanktabellen des Moduls zurück
     *
     * @return array
     */
    public function removeTables();

    /**
     * Liefert ein Array mit den zu erstellenden Moduleinstellungen zurück
     *
     * @return array
     */
    public function settings();

    /**
     * @return array
     */
    public function specialResources();

    /**
     * @return string
     */
    public function getModuleName();

    /**
     * @return int
     */
    public function getSchemaVersion();
}
