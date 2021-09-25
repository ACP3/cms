<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

interface SchemaInterface
{
    /**
     * Liefert ein Array mit den zu erstellenden Datenbanktabellen des Moduls zurück.
     *
     * @return string[]
     */
    public function createTables(): array;

    /**
     * Liefert ein Array mit den zu löschenden Datenbanktabellen des Moduls zurück.
     *
     * @return string[]
     */
    public function removeTables(): array;

    /**
     * Liefert ein Array mit den zu erstellenden Moduleinstellungen zurück.
     *
     * @return array<string, string|int|bool|float>
     */
    public function settings(): array;

    /**
     * @return array<string, array<string, array<string, int>>>
     */
    public function specialResources(): array;

    public function getModuleName(): string;
}
